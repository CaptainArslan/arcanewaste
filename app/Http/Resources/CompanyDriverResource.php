<?php

namespace App\Http\Resources;

use App\Enums\EmploymentTypeEnum;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyDriverResource extends JsonResource
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
            'name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'dob' => $this->dob ?? null,
            'gender' => $this->gender ?? null,
            'image' => $this->image ?? null,
            'license_number' => $this->license_number,
            'license_expires_at' => $this->license_expires_at ?? null,
            'identity_document' => $this->identity_document ?? null,
            'identity_expires_at' => $this->identity_expires_at ?? null,
            'hired_at' => $this->pivot->hired_at ?? null,
            'terminated_at' => $this->pivot->terminated_at ?? null,
            'hourly_rate' => (float) $this->pivot->hourly_rate ?? Driver::DEFAULT_HOURLY_RATE,
            'duty_hours' => (float) $this->pivot->duty_hours ?? Driver::DEFAULT_DUTY_HOURS,
            'employment_type' => $this->pivot->employment_type ?? EmploymentTypeEnum::FULL_TIME->value,
            'is_active' => $this->pivot->is_active ?? true,
        ];
    }
}
