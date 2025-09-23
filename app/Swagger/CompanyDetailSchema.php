<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="CompanyResource",
 *     type="object",
 *     title="Company Details",
 *     description="Company details with related resources",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="ABC Waste Management"),
 *     @OA\Property(property="email", type="string", format="email", example="info@abcwaste.com"),
 *     @OA\Property(property="phone", type="string", example="+923001234567"),
 *     @OA\Property(property="slug", type="string", example="abc-waste"),
 *     @OA\Property(property="customer_panel_url", type="string", nullable=true, example="https://customer.abcwaste.com"),
 *     @OA\Property(property="logo", type="string", nullable=true, example="https://cdn.example.com/logo.png"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Waste management company in Karachi"),
 *     @OA\Property(property="website", type="string", nullable=true, example="https://abcwaste.com"),
 *     @OA\Property(property="onboarding_status", type="string", example="pending"),
 *     @OA\Property(property="onboarding_notes", type="string", nullable=true, example="Awaiting verification"),
 *     @OA\Property(property="onboarding_completed_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(
 *         property="general_settings",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/GeneralSettingResource")
 *     ),
 *
 *     @OA\Property(
 *         property="timings",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/TimingResource")
 *     ),
 *
 *     @OA\Property(
 *         property="holidays",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/HolidayResource")
 *     ),
 *
 *     @OA\Property(
 *         property="payment_options",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/PaymentOptionResource")
 *     ),
 *
 *     @OA\Property(
 *         property="documents",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/DocumentResource")
 *     ),
 *
 *     @OA\Property(
 *         property="latest_location",
 *         ref="#/components/schemas/LatestLocationResource",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="addresses",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/AddressResource")
 *     ),
 *
 *     @OA\Property(
 *         property="warehouses",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/WarehouseResource")
 *     ),
 *
 *     @OA\Property(
 *         property="company_default_address",
 *         ref="#/components/schemas/DefaultAddressResource",
 *         nullable=true
 *     )
 * )
 */
class CompanyDetailSchema
{
    // This class can remain empty; it's just for Swagger annotations
}
