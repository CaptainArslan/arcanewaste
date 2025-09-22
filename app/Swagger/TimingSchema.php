<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="TimingResource",
 *     type="object",
 *     title="Timing",
 *     description="Timing resource response",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="day_of_week", type="string", example="monday"),
 *     @OA\Property(property="opens_at", type="string", example="09:00"),
 *     @OA\Property(property="closes_at", type="string", example="17:00"),
 *     @OA\Property(property="is_closed", type="boolean", example=false)
 * )
 */

class TimingSchema
{
    // This class can remain empty; it's just for Swagger annotations
}