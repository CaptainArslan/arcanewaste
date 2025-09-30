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
        $pivot = $this->pivot; // direct pivot access, safe in belongsToMany

        return [
            'id' => $this->id,

            // Company-specific overrides, fallback to global if not present
            'full_name' => $pivot->full_name ?? $this->full_name,
            'email' => $this->email,
            'image' => $pivot->image ?? $this->image,
            'phone' => $pivot->phone ?? $this->phone,

            // Company-specific status
            'is_active' => $pivot->is_active ?? true,
            'is_delinquent' => $pivot->is_delinquent ?? false,
            'delinquent_days' => $pivot->delinquent_days ?? 0,

            // Emergency contacts
            'emergency_contacts' => EmergencyContactResource::collection(
                $this->whenLoaded('emergencyContacts')
            ),

            // Pivot timestamps
            'attached_at' => $pivot->created_at ?? null,
            'updated_at' => $pivot->updated_at ?? null,
        ];
    }
}
