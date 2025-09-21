<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="Company",
 *     type="object",
 *     title="Company",
 *     description="Company resource response",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Acme Inc."),
 *     @OA\Property(property="email", type="string", format="email", example="company@example.com"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="address", type="string", example="123 Business St, NY"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-22T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-22T10:00:00Z")
 * )
 */
class CompanySchema
{
    // This class can remain empty; it's just for Swagger annotations
}
