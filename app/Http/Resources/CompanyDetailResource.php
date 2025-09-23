<?php

namespace App\Http\Resources;

use App\Enums\FinixOnboardingStatusEnums;
use Illuminate\Http\Request;
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
            'slug' => $this->slug ?? null,
            'customer_panel_url' => $this->customer_panel_url ?? null,
            'logo' => $this->logo ?? null,
            'description' => $this->description ?? null,
            'website' => $this->website ?? null,
            'onboarding_status' => $this->onboarding_status ?? FinixOnboardingStatusEnums::PENDING->value,
            // 'finix_identity_id' => $this->finix_identity_id ?? null,
            // 'finix_merchant_id' => $this->finix_merchant_id ?? null,
            // 'finix_onboarding_form_id' => $this->finix_onboarding_form_id ?? null,
            // 'finix_onboarding_url' => $this->finix_onboarding_url ?? null,
            // 'finix_onboarding_url_expired_at' => $this->finix_onboarding_url_expired_at ?? null,
            'onboarding_status' => $this->finix_onboarding_status ?? FinixOnboardingStatusEnums::PENDING->value,
            'onboarding_notes' => $this->finix_onboarding_notes ?? null,
            'onboarding_completed_at' => $this->finix_onboarding_completed_at ?? null,
            'is_active' => $this->is_active ?? true,
            // relationships
            'general_settings' => GeneralSettingResource::collection($this->generalSettings) ?? [],
            // 'payment_methods' => $this->companyPaymentMethods ?? [],
            // 'merchant_onboarding_logs' => $this->merchantOnboardingLogs ?? [],
            'timings' => TimingResource::collection($this->timings) ?? [],
            'holidays' => HolidayResource::collection($this->holidays) ?? [],
            'payment_options' => PaymentOptionResource::collection($this->paymentOptions) ?? [],
            'documents' => DocumentResource::collection($this->documents) ?? [],
            'latest_location' => LastestLocationResource::make($this->latestLocation) ?? null,
            'addresses' => AddressResource::collection($this->addresses) ?? [],
            'warehouses' => WarehouseResource::collection($this->warehouses) ?? [],
            'company_default_address' => DefaultAddressResource::make($this->defaultAddress) ?? null,
        ];
    }
}
