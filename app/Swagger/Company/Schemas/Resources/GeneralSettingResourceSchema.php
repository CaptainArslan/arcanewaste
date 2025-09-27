<?php

namespace App\Swagger\Company\Schemas\Resources;

/**
 * @OA\Schema(
 *     schema="CompanyGeneralSettingResource",
 *     type="object",
 *     title="Company General Setting Resource",
 *     description="General setting resource for company configuration",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="Unique setting identifier"),
 *     @OA\Property(property="key", type="string", example="business_hours", description="Setting key identifier"),
 *     @OA\Property(property="value", type="string", example="9:00-17:00", description="Setting value"),
 *     @OA\Property(property="type", type="string", enum={"string","integer","boolean","json","array"}, example="string", description="Value data type"),
 *     @OA\Property(property="description", type="string", example="Company business hours", description="Setting description"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Whether setting is active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-25T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-25T10:00:00Z")
 * )
 */
class GeneralSettingResourceSchema
{
    // This class is only for Swagger annotations
}
