<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="DefaultAddressResource",
 *     type="object",
 *     title="Default Address",
 *     description="Default address resource response",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="label", type="string", example="label"),
 *     @OA\Property(property="address_line1", type="string", example="Address Line 1"),
 *     @OA\Property(property="address_line2", type="string", example="Address Line 2"),
 *     @OA\Property(property="city", type="string", example="City"),
 *     @OA\Property(property="state", type="string", example="State"),
 *     @OA\Property(property="country", type="string", example="Country"),
 *     @OA\Property(property="zip", type="string", example="Zip"),
 *     @OA\Property(property="latitude", type="number", example=10.0000000),
 *     @OA\Property(property="longitude", type="number", example=10.0000000),
 *     @OA\Property(property="is_primary", type="boolean", example=false)
 * )
 */

class DefaultAddressResourceSchema
{
    // This class can remain empty; it's just for Swagger annotations
}