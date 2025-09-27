<?php

namespace App\Swagger\Company\Schemas\Resources;

/**
 * @OA\Schema(
 *     schema="CompanyPaymentOptionResource",
 *     type="object",
 *     title="Company Payment Option Resource",
 *     description="Payment option resource for company billing and customer payments",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="Unique payment option identifier"),
 *     @OA\Property(property="name", type="string", example="Credit Card", description="Payment option name"),
 *     @OA\Property(property="type", type="string", enum={"credit_card","debit_card","bank_transfer","cash","check"}, example="credit_card", description="Payment method type"),
 *     @OA\Property(property="percentage", type="number", format="float", example=100.00, description="Percentage of total amount this option covers"),
 *     @OA\Property(property="description", type="string", example="Accept all major credit cards", description="Payment option description"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Whether payment option is active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-25T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-25T10:00:00Z")
 * )
 */
class PaymentOptionResourceSchema
{
    // This class is only for Swagger annotations
}
