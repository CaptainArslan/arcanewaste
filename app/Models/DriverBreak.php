<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverBreak extends Model
{
    protected $fillable = ['driver_attendance_id', 'break_start', 'break_end', 'duration_minutes'];

    public function attendance()
    {
        return $this->belongsTo(DriverAttendance::class);
    }
}
