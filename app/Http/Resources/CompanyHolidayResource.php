<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyHolidayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'company_id'      => $this->holidayable_id, // since holidayable is Company
            'name'            => $this->name,
            'date'            => $this->date,
            'recurrence_type' => $this->recurrence_type, // e.g., 'none', 'weekly', 'monthly', 'yearly'
            'day_of_week'     => $this->day_of_week !== null
                ? Carbon::create()->startOfWeek()->addDays($this->day_of_week)->format('l')
                : null, // convert 0 → Sunday, etc.
            'month_day'       => $this->month_day,
            'reason'          => $this->reason,
            'is_approved'     => $this->is_approved,
            'is_active'       => $this->is_active,
        ];
    }
}
