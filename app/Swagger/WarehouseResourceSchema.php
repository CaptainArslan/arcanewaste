<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="WarehouseResource",
 *     type="object",
 *     title="Warehouse",
 *     description="Warehouse resource response",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Warehouse"),
 *     @OA\Property(property="code", type="string", example="code"),
 *     @OA\Property(property="type", type="string", example="type"),
 *     @OA\Property(property="capacity", type="integer", example=100),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="address", type="object", example={"address_line1": "123 Main St", "city": "New York", "country": "USA"}),
 * )
 */
class WarehouseResourceSchema
{
    // This class can remain empty; it's just for Swagger annotations
}
