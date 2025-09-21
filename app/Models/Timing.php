<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Timing extends Model
{
    use HasFactory;

    protected $fillable = [
        'timeable_id',
        'timeable_type',
        'day_of_week',
        'opens_at',
        'closes_at',
        'is_closed',
    ];

    protected $casts = [
        'opens_at' => 'datetime:H:i',
        'closes_at' => 'datetime:H:i',
        'is_closed' => 'boolean',
    ];

    // Relationships
    public function timeable(): MorphTo
    {
        return $this->morphTo();
    }
}
