<?php

namespace App\Swagger\Company\Schemas\Components;

/**
 * @OA\Schema(
 *     schema="Warehouse",
 *     type="object",
 *     title="Warehouse",
 *     description="Warehouse information",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="Warehouse ID"),
 *     @OA\Property(property="name", type="string", example="Main Warehouse", description="Warehouse name"),
 *     @OA\Property(property="code", type="string", example="WH-001", description="Warehouse code"),
 *     @OA\Property(property="type", type="string", example="storage", description="Warehouse type"),
 *     @OA\Property(property="capacity", type="integer", example=100, description="Storage capacity"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Active status")
 * )
 */
class WarehouseSchema
{
    // This class is only for Swagger annotations
}
