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
        $pivot = $this->pivot ?? null;

        return [
            'id'               => $this->id,
            'name'             => $this->full_name,
            'email'            => $this->email,
            'phone'            => $this->phone,
            'dob'              => $this->dob,
            'gender'           => $this->gender,
            'image'            => $this->image,
            'license_number'   => $this->license_number,
            'license_expires_at' => $this->license_expires_at,
            'identity_document'  => $this->identity_document,
            'identity_expires_at' => $this->identity_expires_at,

            // Pivot fields (safe fallback if pivot is null)
            'hired_at'        => $pivot->hired_at ?? null,
            'terminated_at'   => $pivot->terminated_at ?? null,
            'hourly_rate'     => $pivot?->hourly_rate ? (float) $pivot->hourly_rate : Driver::DEFAULT_HOURLY_RATE,
            'duty_hours'      => $pivot?->duty_hours ? (float) $pivot->duty_hours : Driver::DEFAULT_DUTY_HOURS,
            'employment_type' => $pivot->employment_type ?? EmploymentTypeEnum::FULL_TIME->value,
            'is_active'       => $pivot->is_active ?? true,
        ];
    }
}
