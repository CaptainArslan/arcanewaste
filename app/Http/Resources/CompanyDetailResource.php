<?php

namespace App\Http\Resources;

use App\Enums\FinixOnboardingStatusEnum;
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
            'onboarding_status' => $this->onboarding_status ?? FinixOnboardingStatusEnum::PENDING->value,
            'onboarding_status' => $this->finix_onboarding_status ?? FinixOnboardingStatusEnum::PENDING->value,
            'onboarding_notes' => $this->finix_onboarding_notes ?? null,
            'onboarding_completed_at' => $this->finix_onboarding_completed_at ?? null,
            'is_active' => $this->is_active ?? true,
            // relationships
            'default_address' => DefaultAddressResource::make($this->defaultAddress),
            'general_settings' => GeneralSettingResource::collection($this->generalSettings),
            'timings' => TimingResource::collection($this->timings),
            'holidays' => CompanyHolidayResource::collection($this->holidays),
            'payment_options' => PaymentOptionResource::collection($this->paymentOptions),
            'documents' => DocumentResource::collection($this->documents),
            'addresses' => AddressResource::collection($this->addresses),
            'warehouses' => WarehouseResource::collection($this->warehouses),
        ];
    }
}
