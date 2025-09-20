<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverTimeSchedule extends Model
{
    protected $fillable = [
        'company_id',
        'driver_id',
        'schedule_date',
        'start_time',
        'end_time',
        'is_holiday',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
