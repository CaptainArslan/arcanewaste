<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HolidayResource extends JsonResource
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
            'date' => $this->date,
            'recurrence_type' => $this->recurrence_type,
            'day_of_week' => $this->day_of_week,
            'month_day' => $this->month_day,
            'reason' => $this->reason,
            'is_approved' => $this->is_approved,
            'is_active' => $this->is_active,
        ];
    }
}
