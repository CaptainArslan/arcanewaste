<?php

namespace App\Swagger\Company\Schemas\Resources;

/**
 * @OA\Schema(
 *     schema="CompanyTaxResource",
 *     type="object",
 *     title="Company Tax Resource",
 *     description="Tax resource for company pricing and billing",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="Unique tax identifier"),
 *     @OA\Property(property="name", type="string", example="Sales Tax", description="Tax name"),
 *     @OA\Property(property="type", type="string", enum={"percentage","fixed"}, example="percentage", description="Tax calculation type"),
 *     @OA\Property(property="rate", type="number", format="float", example=10.00, description="Tax rate (percentage or fixed amount)"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Whether tax is active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-25T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-25T10:00:00Z")
 * )
 */
class TaxResourceSchema
{
    // This class is only for Swagger annotations
}
