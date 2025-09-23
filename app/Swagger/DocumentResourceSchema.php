<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="DocumentResource",
 *     type="object",
 *     title="Document",
 *     description="Document resource response",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Document"),
 *     @OA\Property(property="type", type="string", example="type"),
 *     @OA\Property(property="file_path", type="string", example="file_path"),
 *     @OA\Property(property="mime_type", type="string", example="mime_type"),
 *     @OA\Property(property="issued_at", type="string", format="date", example="2025-01-01"),
 *     @OA\Property(property="expires_at", type="string", format="date", example="2025-01-01"),
 *     @OA\Property(property="is_verified", type="boolean", example=false)
 * )
 */
class DocumentResourceSchema
{
    // This class can remain empty; it's just for Swagger annotations
}
