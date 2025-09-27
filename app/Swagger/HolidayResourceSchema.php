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
 *     @OA\Property(property="name", type="string", example="New Year's Day"),
 *     @OA\Property(property="date", type="string", format="date", nullable=true, example="2025-01-01", description="Specific holiday date if one-off"),
 *     @OA\Property(property="recurrence_type", type="string", enum={"none","weekly","yearly"}, example="yearly", description="Recurrence pattern of the holiday"),
 *     @OA\Property(property="day_of_week", type="integer", nullable=true, example=1, description="0=Sunday .. 6=Saturday (for weekly recurrence)"),
 *     @OA\Property(property="day_of_week_name", type="string", nullable=true, example="Monday", description="Human-readable day of week"),
 *     @OA\Property(property="month_day", type="string", nullable=true, example="01-01", description="Month and day for yearly recurrence (MM-DD)"),
 *     @OA\Property(property="reason", type="string", nullable=true, example="Public holiday"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T12:00:00Z")
 * )
 */
class HolidayResourceSchema
{
    // This class is only for Swagger annotations
}
