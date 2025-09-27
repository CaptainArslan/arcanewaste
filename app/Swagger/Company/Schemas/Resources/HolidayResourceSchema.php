<?php

namespace App\Swagger\Company\Schemas\Resources;

/**
 * @OA\Schema(
 *     schema="CompanyHolidayResource",
 *     type="object",
 *     title="Company Holiday Resource",
 *     description="Company holiday resource response with polymorphic support",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="Unique holiday identifier"),
 *     @OA\Property(property="company_id", type="integer", example=2, description="Company ID (from holidayable_id)"),
 *     @OA\Property(property="name", type="string", example="Independence Day", description="Holiday name"),
 *     @OA\Property(property="date", type="string", format="date", example="2025-08-14", description="Specific holiday date for one-off holidays"),
 *     @OA\Property(property="recurrence_type", type="string", enum={"none","weekly","yearly"}, example="yearly", description="Recurrence pattern"),
 *     @OA\Property(property="day_of_week", type="string", example="Friday", description="Human-readable day name for weekly holidays"),
 *     @OA\Property(property="month_day", type="string", example="08-14", description="Month-day format for yearly holidays (MM-DD)"),
 *     @OA\Property(property="reason", type="string", example="National holiday", description="Holiday description or reason"),
 *     @OA\Property(property="is_approved", type="string", enum={"pending","approved","rejected"}, example="approved", description="Approval status"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Whether holiday is active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-25T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-25T10:00:00Z")
 * )
 */
class HolidayResourceSchema
{
    // This class is only for Swagger annotations
}
