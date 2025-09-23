<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="HolidayResource",
 *     type="object",
 *     title="Holiday",
 *     description="Holiday resource response",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Holiday"),
 *     @OA\Property(property="holiday_date", type="string", format="date", example="2025-01-01"),
 *     @OA\Property(property="description", type="string", example="Holiday description"),
 *     @OA\Property(property="is_recurring", type="boolean", example=false)
 * )
 */
class HolidayResourceSchema
{
    // This class can remain empty; it's just for Swagger annotations
}
