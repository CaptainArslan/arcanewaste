<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LatestLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locatable_id',
        'locatable_type',
        'latitude',
        'longitude',
        'address',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'recorded_at' => 'datetime',
    ];

    protected $hidden = [
        'locatable_id',
        'locatable_type',
    ];

    // Relationships
    public function locatable(): MorphTo
    {
        return $this->morphTo();
    }
}
