<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="DumpsterSizeResource",
 *     type="object",
 *     title="Dumpster Size",
 *     description="Dumpster size resource response",
 *     @OA\Property(property="id", type="integer", example=2),
 *     @OA\Property(property="name", type="string", example="Large Dumpster"),
 *     @OA\Property(property="code", type="string", example="LD-10YD"),
 *     @OA\Property(property="description", type="string", example="10 cubic yard dumpster for large projects"),
 *     @OA\Property(property="min_rental_days", type="integer", example=2),
 *     @OA\Property(property="max_rental_days", type="integer", example=14),
 *     @OA\Property(property="base_rent", type="number", format="float", example=250.00),
 *     @OA\Property(property="extra_day_rent", type="number", format="float", example=25.00),
 *     @OA\Property(property="overdue_rent", type="number", format="float", example=50.00),
 *     @OA\Property(property="volume_cubic_yards", type="number", format="float", example=10.00),
 *     @OA\Property(property="weight_limit_lbs", type="integer", example=2000),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
 *     @OA\Property(property="taxes", type="array", @OA\Items(ref="#/components/schemas/TaxResource")),
 *     @OA\Property(property="company", type="object", ref="#/components/schemas/Company"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-25T05:53:01.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-25T05:53:01.000000Z")
 * )
 */
class DumpsterSizeSchema
{
    // This class is used only for Swagger annotations
}
