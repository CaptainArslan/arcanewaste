<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverOverTime extends Model
{
    protected $fillable = ['driver_attendance_id', 'hours', 'rate', 'amount', 'is_approved'];

    public function attendance()
    {
        return $this->belongsTo(DriverAttendance::class);
    }
}
