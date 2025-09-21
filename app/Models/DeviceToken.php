<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'deviceable_id',
        'deviceable_type',
        'device_token',
        'device_type',
        'device_id',
    ];

    protected $casts = [
        'deviceable_id' => 'integer',
        'deviceable_type' => 'string',
        'device_token' => 'string',
        'device_type' => 'string',
        'device_id' => 'string',
    ];

    protected $hidden = [
        'deviceable_id',
        'deviceable_type',
    ];

    // Relationships
    public function deviceable(): MorphTo
    {
        return $this->morphTo();
    }
}
