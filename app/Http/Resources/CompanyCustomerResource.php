<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyCustomerResource extends JsonResource
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
            'full_name' => $this->pivot->full_name ?? $this->full_name, // Global name
            'email' => $this->email, // Global email
            'image' => $this->pivot->image ?? $this->image, // Company-specific or fallback
            'phone' => $this->pivot->phone ?? $this->phone, // Company-specific or fallback

            // Company-specific status fields
            'is_active' => $this->pivot->is_active ?? true,
            'is_delinquent' => $this->pivot->is_delinquent ?? false,
            'delinquent_days' => $this->pivot->delinquent_days ?? 0,

            // Emergency contacts
            'emergency_contacts' => EmergencyContactResource::collection($this->emergencyContacts),

            // Pivot timestamps if needed
            'attached_at' => $this->pivot->created_at ?? null,
            'updated_at' => $this->pivot->updated_at ?? null,
        ];
    }
}
