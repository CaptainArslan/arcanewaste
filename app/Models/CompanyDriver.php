<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDriver extends Model
{
    protected $table = 'company_driver';

    protected $fillable = [
        'company_id',
        'driver_id',
        'full_name',
        'phone',
        'image',
        'is_active',
        'hired_at',
        'hourly_rate',
        'employment_type',
        'terminated_at',
        'created_at',
        'updated_at',
    ];

    // Relations
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
