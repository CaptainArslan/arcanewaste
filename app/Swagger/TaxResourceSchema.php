<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="TaxResource",
 *     type="object",
 *     title="Tax",
 *     description="Tax resource response",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Tax"),
 *     @OA\Property(property="type", type="string", example="type"),
 *     @OA\Property(property="rate", type="number", example=10),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 */
class TaxResourceSchema
{
    // This class can remain empty; it's just for Swagger annotations
}
