<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{

    public function store(Request $request): JsonResponse
{
    // 1) Validate the incoming request
    $validated = Validator::make($request->all(), [
        'media' => ['required', 'array', 'min:1'],
        'media.*' => ['file', 'max:2048'], // 2MB per file
        'remove_paths' => ['nullable', 'array'],
        'remove_paths.*' => ['nullable', 'string'],
    ], [
        'media.required' => 'At least one media file is required.',
        'media.*.file' => 'Each media item must be a valid file.',
        'media.*.max' => 'A media file may not be greater than 2048 kilobytes.',
    ]);

    if ($validated->fails()) {
        return $this->sendErrorResponse($validated->errors()->first(), Response::HTTP_BAD_REQUEST);
    }

    try {
        // 2) Normalize keys to delete
        $keysToDelete = collect($request->input('remove_paths', []))
            ->filter(fn($path) => !empty($path))
            ->map(fn($raw) => normalizeS3Key($raw))
            ->filter() // remove nulls/invalids
            ->values();

        // 3) Delete files in bulk
        $failedDeletes = collect(); // FIX: always a Collection
        if ($keysToDelete->isNotEmpty()) {
            Storage::disk('s3')->delete($keysToDelete->toArray());

            // Verify deletion failures
            $failedDeletes = $keysToDelete->filter(fn($k) => Storage::disk('s3')->exists($k))->values();
        }

        // 4) Upload new files
        $uploadedUrls = collect($request->file('media'))
            ->map(function ($file) {
                if (!$file->isValid()) {
                    throw new \RuntimeException('One of the uploaded files is invalid.');
                }
                // Store file and get the S3 path
                return $file->store('media', 's3');
            })
            ->toArray(); // FIX: convert to plain array

        // 5) Build payload
        $payload = [
            'uploaded' => $uploadedUrls,
            'deleted' => $keysToDelete->diff($failedDeletes)->values()->toArray(),
            'failed_deletes' => $failedDeletes->toArray(),
        ];

        return $this->sendSuccessResponse($payload, 'Media processed successfully.', Response::HTTP_OK);

    } catch (\Throwable $e) {
        Log::error('Media upload failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return $this->sendErrorResponse('Failed to process media.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}
