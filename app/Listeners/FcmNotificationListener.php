<?php

namespace App\Listeners;

use App\Events\FcmNotificationEvent;
use Exception;
use Google_Client as GoogleClient;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class FcmNotificationListener implements ShouldDispatchAfterCommit, ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FcmNotificationEvent $event): void
    {
        $tokens = $event->deviceTokens;
        $model = $event->model;
        $title = $event->title;
        $body = $event->body;
        $rawData = $event->data ?? [];

        // save the notification to the database

        // Initialize statistics
        $stats = [
            'total_tokens' => count($tokens),
            'successful' => 0,
            'failed' => 0,
            'invalid_tokens' => 0,
            'network_errors' => 0,
            'server_errors' => 0,
            'other_errors' => 0,
        ];

        if (empty($tokens)) {
            Log::warning('⚠️ No FCM tokens provided for notification', [
                'title' => $title,
                'body' => $body,
            ]);

            return;
        }

        try {
            // Validate configuration
            $projectId = config('services.fcm.project_id');
            $credentialsFilePath = config('services.fcm.credentials_file_path');

            Log::info($credentialsFilePath);

            if (empty($projectId)) {
                Log::error('❌ FCM project_id not configured');

                return;
            }

            if (empty($credentialsFilePath) || ! file_exists($credentialsFilePath)) {
                Log::error('❌ FCM credentials file not found', [
                    'path' => $credentialsFilePath,
                ]);

                return;
            }

            // Validate credentials file content
            $credentialsContent = file_get_contents($credentialsFilePath);
            $credentials = json_decode($credentialsContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('❌ FCM credentials file is not valid JSON', [
                    'path' => $credentialsFilePath,
                    'json_error' => json_last_error_msg(),
                ]);

                return;
            }

            if (empty($credentials['private_key']) || strpos($credentials['private_key'], '...') !== false) {
                Log::error('❌ FCM credentials file has invalid or truncated private key', [
                    'path' => $credentialsFilePath,
                    'has_private_key' => ! empty($credentials['private_key']),
                    'is_truncated' => strpos($credentials['private_key'], '...') !== false,
                ]);

                return;
            }

            // Initialize Google Client
            $client = new GoogleClient;
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $token = $client->getAccessToken();

            $access_token = $token['access_token'];

            if (empty($access_token)) {
                Log::error('❌ Failed to obtain FCM access token');

                return;
            }

            $headers = [
                "Authorization: Bearer $access_token",
                'Content-Type: application/json',
            ];

            // Sanitize data: must be a flat map of string keys and string values
            $sanitizedData = [];

            if (! empty($rawData) && (is_array($rawData) || is_object($rawData))) {
                foreach ($rawData as $key => $value) {
                    // Only process string keys
                    if (! is_string($key)) {
                        continue;
                    }

                    // Skip arrays and objects, only allow primitive values
                    if (is_array($value) || is_object($value)) {
                        continue;
                    }

                    // Convert to string and ensure it's not empty
                    $stringValue = strval($value);
                    if ($stringValue !== '') {
                        $sanitizedData[$key] = $stringValue;
                    }
                }
            }

            // Process each token
            foreach ($tokens as $index => $fcm) {
                $tokenIndex = $index + 1;

                // Validate token format
                if (empty($fcm) || strlen($fcm) < 100) {
                    $stats['invalid_tokens']++;
                    $stats['failed']++;

                    continue;
                }

                // Build message payload
                $message = [
                    'message' => [
                        'token' => $fcm,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'android' => [
                            'priority' => 'high',
                            'ttl' => '3600s',
                        ],
                        'apns' => [
                            'headers' => [
                                'apns-priority' => '10',
                            ],
                        ],
                    ],
                ];

                // Only add data field if we have sanitized data
                if (! empty($sanitizedData)) {
                    $message['message']['data'] = $sanitizedData;
                }

                $payload = json_encode($message);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error("❌ JSON encoding failed for token {$tokenIndex}", [
                        'token' => substr($fcm, 0, 20).'...',
                        'json_error' => json_last_error_msg(),
                        'message_structure' => $message,
                    ]);
                    $stats['other_errors']++;
                    $stats['failed']++;

                    continue;
                }

                // Validate payload structure
                $decodedPayload = json_decode($payload, true);
                if (! isset($decodedPayload['message']['token']) || ! isset($decodedPayload['message']['notification'])) {
                    Log::error("❌ Invalid payload structure for token {$tokenIndex}", [
                        'token' => substr($fcm, 0, 20).'...',
                        'payload_structure' => $decodedPayload,
                    ]);
                    $stats['other_errors']++;
                    $stats['failed']++;

                    continue;
                }

                // Send notification via cURL
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_CONNECTTIMEOUT => 10,
                ]);

                $startTime = microtime(true);
                $response = curl_exec($ch);
                $endTime = microtime(true);
                $responseTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds

                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $err = curl_error($ch);
                $curlInfo = curl_getinfo($ch);
                curl_close($ch);

                $responseBody = json_decode($response, true);

                // Handle different response scenarios
                if ($err) {
                    Log::error("❌ cURL error for token {$tokenIndex}", [
                        'token' => substr($fcm, 0, 20).'...',
                        'error' => $err,
                        'curl_info' => [
                            'total_time' => $curlInfo['total_time'],
                            'connect_time' => $curlInfo['connect_time'],
                            'namelookup_time' => $curlInfo['namelookup_time'],
                        ],
                    ]);
                    $stats['network_errors']++;
                    $stats['failed']++;
                } elseif ($httpCode >= 200 && $httpCode < 300 && isset($responseBody['name'])) {
                    Log::info('✅ FCM sent successfully', [
                        'token' => substr($fcm, 0, 20).'...',
                        'message_id' => $responseBody['name'],
                    ]);
                    $stats['successful']++;
                } else {
                    // Handle various error scenarios
                    $errorStatus = $responseBody['error']['status'] ?? 'UNKNOWN';
                    $errorMessage = $responseBody['error']['message'] ?? 'No error message';
                    $errorCode = $responseBody['error']['code'] ?? 0;
                    // Categorize errors
                    if (in_array($errorStatus, ['UNREGISTERED', 'INVALID_ARGUMENT', 'NOT_FOUND'])) {
                        $stats['invalid_tokens']++;
                    } elseif ($httpCode >= 500) {
                        $stats['server_errors']++;
                    } else {
                        $stats['other_errors']++;
                    }

                    $stats['failed']++;
                }

                // Add delay between requests to prevent rate limiting
                if ($tokenIndex < $stats['total_tokens']) {
                    sleep(1);
                }
            }

            // Log final statistics
            $successRate = $stats['total_tokens'] > 0 ? round(($stats['successful'] / $stats['total_tokens']) * 100, 2) : 0;

            Log::info('📊 FCM batch completed', [
                'total' => $stats['total_tokens'],
                'successful' => $stats['successful'],
                'failed' => $stats['failed'],
                'success_rate' => $successRate.'%',
                'invalid_tokens' => $stats['invalid_tokens'],
            ]);
        } catch (Exception $e) {
            Log::error('🔥 FCM Exception: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }
}
