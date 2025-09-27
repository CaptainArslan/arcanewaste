<?php

namespace App\Swagger\Company\Schemas\Components;

/**
 * @OA\Schema(
 *     schema="DumpsterSize",
 *     type="object",
 *     title="Dumpster Size",
 *     description="Dumpster size specifications and pricing",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="Size ID"),
 *     @OA\Property(property="name", type="string", example="10 Yard Dumpster", description="Size name"),
 *     @OA\Property(property="code", type="string", example="10YD", description="Size code"),
 *     @OA\Property(property="description", type="string", example="10 cubic yard dumpster for medium projects", description="Size description"),
 *     @OA\Property(property="min_rental_days", type="integer", example=1, description="Minimum rental period"),
 *     @OA\Property(property="max_rental_days", type="integer", example=14, description="Maximum rental period"),
 *     @OA\Property(property="base_rent", type="number", format="float", example=100.00, description="Base rental cost"),
 *     @OA\Property(property="extra_day_rent", type="number", format="float", example=10.00, description="Cost per extra day"),
 *     @OA\Property(property="overdue_rent", type="number", format="float", example=20.00, description="Overdue penalty rate"),
 *     @OA\Property(property="volume_cubic_yards", type="integer", example=10, description="Volume capacity"),
 *     @OA\Property(property="weight_limit_lbs", type="integer", example=2000, description="Weight limit in pounds"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Active status")
 * )
 */
class DumpsterSizeSchema
{
    // This class is only for Swagger annotations
}
