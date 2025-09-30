<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverHolidayResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'driver_id' => $this->holidayable_id, // since holidayable is Driver
            'name' => $this->name,
            'date' => $this->date,
            'recurrence_type' => $this->recurrence_type,
            'day_of_week' => $this->day_of_week !== null
                ? \Carbon\Carbon::create()->startOfWeek()->addDays($this->day_of_week)->format('l')
                : null,
            'month_day' => $this->month_day,
            'description' => $this->description,
            'status' => $this->status ?? 'pending', // if driver holiday needs approval
        ];
    }
}
