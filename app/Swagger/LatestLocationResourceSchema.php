<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="LatestLocationResource",
 *     type="object",
 *     title="Latest Location",
 *     description="Latest location resource response",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="latitude", type="number", example=10.0000000),
 *     @OA\Property(property="longitude", type="number", example=10.0000000),
 *     @OA\Property(property="address", type="string", example="Address"),
 *     @OA\Property(property="recorded_at", type="string", format="date-time", example="2025-01-01T00:00:00Z")
 * )
 */

class LatestLocationResourceSchema
{
    // This class can remain empty; it's just for Swagger annotations
}