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
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'customer_is_active' => $this->is_active, // From customer table
            // Pivot fields
            'company_is_active' => $this->pivot->is_active ?? null,
            'company_is_delinquent' => $this->pivot->is_delinquent ?? null,
            'company_delinquent_days' => $this->pivot->delinquent_days ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
