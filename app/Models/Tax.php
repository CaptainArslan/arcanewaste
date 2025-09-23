<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'rate',
        'is_active',
    ];

    protected $casts = [
        'type' => 'string',
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function dumpsterSizes(): BelongsToMany
    {
        return $this->belongsToMany(DumpsterSize::class);
    }
}
