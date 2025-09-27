<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="CompanyHolidayResourceSchema",
 *     type="object",
 *     title="Company Holiday",
 *     description="Company holiday resource response",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="company_id", type="integer", example=2),
 *     @OA\Property(property="name", type="string", example="Christmas"),
 *     @OA\Property(property="date", type="string", format="date", example="2025-12-25"),
 *     @OA\Property(property="day_of_week", type="string", example="Monday"),
 *     @OA\Property(property="recurrence_type", type="string", example="yearly"),
 *     @OA\Property(property="reason", type="string", example="Christmas holiday"),
 *     @OA\Property(property="is_approved", type="boolean", example=true),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-25T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-25T10:00:00Z")
 * )
 */
class CompanyHolidayResourceSchema
{
    // This class is just for Swagger annotations
}
