<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAttendance extends Model
{
    protected $fillable = [
        'company_id',
        'driver_id',
        'attendance_date',
        'scheduled_start',
        'scheduled_end',
        'actual_start',
        'actual_end',
        'is_present',
        'is_late',
        'is_overtime',
        'regular_hours',
        'overtime_hours',
        'total_pay',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function breaks()
    {
        return $this->hasMany(DriverBreak::class);
    }

    public function overtimes()
    {
        return $this->hasMany(DriverOvertime::class);
    }
}
