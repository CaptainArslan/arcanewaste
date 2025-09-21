<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinixService
{
    protected ?string $baseUrl;

    protected ?string $username;

    protected ?string $password;

    protected ?string $apiVersion;

    public function __construct()
    {
        $mode = config('services.finix.mode', 'sandbox');

        $this->baseUrl = config("services.finix.{$mode}.base_url");
        $this->username = config("services.finix.{$mode}.user_name");
        $this->password = config("services.finix.{$mode}.password");
        $this->apiVersion = config("services.finix.{$mode}.api_version");
    }

    protected function request(string $method, string $endpoint, array $data = [])
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Finix-Version' => $this->apiVersion,
                ])
                ->baseUrl($this->baseUrl)
                ->send(strtoupper($method), $endpoint, [
                    'json' => $data,
                ])
                ->throw(); // Will throw an exception if response is not 2xx

            return $response->json(); // Return decoded JSON response
        } catch (RequestException $e) {
            Log::error('Finix API Request Failed', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'response' => $e->response?->json() ?? null,
            ]);
            throw $e;
        } catch (Exception $e) {
            Log::error('Finix API Request Failed', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function createIdentity(array $data)
    {
        return $this->request('post', 'identities', $data);
    }

    public function retrieveIdentity(string $identityId)
    {
        return $this->request('get', "identities/{$identityId}");
    }

    public function createMerchant(string $identityId)
    {
        return $this->request('post', "identities/{$identityId}/merchants", [
            'processor' => 'DUMMY_V1',
        ]);
    }

    public function retrieveMerchantByIdentityId(string $identityId, array $params = [])
    {
        $queryString = http_build_query($params);
        $endpoint = "merchants?identity_id={$identityId}";
        if ($queryString) {
            $endpoint .= '?'.$queryString;
        }

        return $this->request('get', $endpoint);
    }

    public function retrieveMerchant(string $merchantId)
    {
        return $this->request('get', "merchants/{$merchantId}");
    }

    public function createPaymentInstrument(array $data)
    {
        return $this->request('post', 'payment_instruments', $data);
    }

    public function createPaymentInstrumentByToken(array $data)
    {
        return $this->request('post', 'payment_instruments', $data);
    }

    public function createTransfer(array $data)
    {
        return $this->request('post', 'transfers', $data);
    }

    public function createOnboardingForm(array $data)
    {
        return $this->request('post', 'onboarding_forms', $data);
    }

    public function retrieveOnboardingForm(string $formId)
    {
        return $this->request('get', "onboarding_forms/{$formId}");
    }

    public function createOrRetrieveOnboardingForm(array $data)
    {
        $form = $this->retrieveOnboardingForm($data['id']);
        if (! $form) {
            return $this->createOnboardingForm($data);
        }

        return $form;
    }

    public function getOnboardingFormUrl(string $formId)
    {
        $form = $this->retrieveOnboardingForm($formId);

        return $form['url'] ?? null;
    }

    public function updateOnboardingForm(string $formId, array $data)
    {
        return $this->request('put', "onboarding_forms/{$formId}", $data);
    }

    public function listOnboardingForms(array $params = [])
    {
        $queryString = http_build_query($params);
        $endpoint = 'onboarding_forms';
        if ($queryString) {
            $endpoint .= '?'.$queryString;
        }

        return $this->request('get', $endpoint);
    }

    public function createWebhookEndpoint(array $data)
    {
        return $this->request('post', 'webhooks', $data);
    }

    public function listWebhookEndpoints()
    {
        return $this->request('get', 'webhooks');
    }

    public function deleteWebhookEndpoint(string $webhookId)
    {
        return $this->request('delete', "webhooks/{$webhookId}");
    }

    public function createSubscription(array $data)
    {
        return $this->request('post', 'subscriptions', $data);
    }

    public function retrieveSubscription(string $subscriptionId)
    {
        return $this->request('get', "subscriptions/{$subscriptionId}");
    }

    public function cancelSubscription(string $subscriptionId)
    {
        return $this->request('delete', "subscriptions/{$subscriptionId}");
    }
}
