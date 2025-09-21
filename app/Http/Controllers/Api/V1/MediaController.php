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
    /**
     * @OA\Info(
     *     version="1.0.0",
     *     title="Rogue Insider API Documentation",
     *     description="Authentication endpoints for Rogue Insider."
     * )
     *
     * @OA\Server(
     *     url=L5_SWAGGER_CONST_HOST,
     *     description="API Base URL"
     * )
     *
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT"
     * )
     * 
     * @OA\Post(
     *     path="/media/upload",
     *     tags={"Media"},
     *     summary="Upload media files to S3 and optionally delete old ones",
     *     description="This endpoint uploads one or more media files to S3 and can also delete old files if `remove_paths` are provided.",
     *
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"media"},
     *                 @OA\Property(
     *                     property="media[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="One or more media files (max 2MB each)"
     *                 ),
     *                 @OA\Property(
     *                     property="remove_paths[]",
     *                     type="array",
     *                     @OA\Items(type="string", example="media/old-file.jpg"),
     *                     description="Optional array of S3 paths to delete"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Media processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Media processed successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(
     *                     property="uploaded",
     *                     type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="path", type="string", example="media/abc123.jpg"),
     *                         @OA\Property(property="name", type="string", example="myfile.jpg"),
     *                         @OA\Property(property="type", type="string", example="jpg"),
     *                         @OA\Property(property="mime_type", type="string", example="image/jpeg"),
     *                         @OA\Property(property="size", type="integer", example=204800),
     *                         @OA\Property(property="url", type="string", example="https://bucket.s3.amazonaws.com/media/abc123.jpg")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="deleted",
     *                     type="array",
     *                     @OA\Items(type="string", example="media/old-file.jpg")
     *                 ),
     *                 @OA\Property(
     *                     property="failed_deletes",
     *                     type="array",
     *                     @OA\Items(type="string", example="media/fail.jpg")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No media file provided.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The media field is required.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to process media. Something went wrong")
     *         )
     *     )
     * )
     */

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
