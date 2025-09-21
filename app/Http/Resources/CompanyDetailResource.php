<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Enums\FinixOnboardingStatusEnums;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address ?? [],
            'slug' => $this->slug ?? null,
            'customer_panel_url' => $this->customer_panel_url ?? null,
            'logo' => $this->logo ?? null,
            'description' => $this->description ?? null,
            'website' => $this->website ?? null,
            'onboarding_status' => $this->onboarding_status ?? FinixOnboardingStatusEnums::PENDING->value,
            'finix_identity_id' => $this->finix_identity_id ?? null,
            'finix_merchant_id' => $this->finix_merchant_id ?? null,
            'finix_onboarding_form_id' => $this->finix_onboarding_form_id ?? null,
            'finix_onboarding_url' => $this->finix_onboarding_url ?? null,
            'finix_onboarding_url_expired_at' => $this->finix_onboarding_url_expired_at ?? null,
            'finix_onboarding_status' => $this->finix_onboarding_status ?? FinixOnboardingStatusEnums::PENDING->value,
            'finix_onboarding_notes' => $this->finix_onboarding_notes ?? null,
            'finix_onboarding_completed_at' => $this->finix_onboarding_completed_at ?? null,
            'is_active' => $this->is_active ?? true,
            // relationships
            'general_settings' => $this->generalSettings ?? [],
            'payment_methods' => $this->paymentMethods ?? [],
            'merchant_onboarding_logs' => $this->merchantOnboardingLogs ?? [],
            'timings' => $this->timings ?? [],
            'dumpster_sizes' => $this->dumpsterSizes ?? [],
            'holidays' => $this->holidays ?? [],
            'payment_options' => $this->paymentOptions ?? [],
            'documents' => $this->documents ?? [],
            'latest_location' => $this->latestLocation ?? null,
            'devices' => $this->devices ?? [],
            'addresses' => $this->addresses ?? [],
            'warehouses' => $this->warehouses ?? [],
            'default_address' => $this->defaultAddress ?? null,
        ];
    }
}
