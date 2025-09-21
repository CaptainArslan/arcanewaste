<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'media'          => ['required', 'array'],
            'media.*'        => ['file', 'max:2048'],
            'remove_paths'   => ['nullable', 'array'],
            'remove_paths.*' => ['nullable', 'string'],
        ], [
            'media.required'  => 'At least one media file is required.',
            'media.*.file'    => 'Each media item must be a valid file.',
            'media.*.max'     => 'A media file may not be greater than 2048 kilobytes.',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            // 1) Delete files if requested
            $keysToDelete   = collect($request->input('remove_paths', []))
                ->filter()
                ->map(fn($raw) => normalizeS3Key($raw))
                ->filter()
                ->values()
                ->all();

            $failedDeletes = [];
            if (!empty($keysToDelete)) {
                $deleteResult = Storage::disk('s3')->delete($keysToDelete);
                if (!$deleteResult) {
                    $failedDeletes = $keysToDelete;
                }
            }

            // 2) Upload new files
            if (!$request->hasFile('media')) {
                return $this->sendErrorResponse('No media file provided.', Response::HTTP_BAD_REQUEST);
            }

            $uploads = [];
            foreach ($request->file('media') as $media) {
                if (!$media->isValid()) {
                    return $this->sendErrorResponse('Invalid media file.', Response::HTTP_BAD_REQUEST);
                }

                $path = $media->store('media', 's3');

                $uploads[] = [
                    'path'       => $path,
                    'name'       => $media->getClientOriginalName(),
                    'type'       => $media->getClientOriginalExtension(),
                    'mime_type'  => $media->getClientMimeType(),
                    'size'       => $media->getSize(),
                    'url'        => Storage::disk('s3')->url($path),
                ];
            }

            $payload = [
                'uploaded'        => $uploads,
                'deleted'         => array_values(array_diff($keysToDelete, $failedDeletes)),
                'failed_deletes'  => $failedDeletes,
            ];

            return $this->sendSuccessResponse($payload, 'Media processed successfully.', Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(
                'Failed to process media. ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
