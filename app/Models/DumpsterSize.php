<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DumpsterSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'min_rental_days',
        'max_rental_days',
        'base_rent',
        'extra_day_rent',
        'overdue_rent',
        'volume_cubic_yards',
        'weight_limit_lbs',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_rental_days' => 'integer',
        'max_rental_days' => 'integer',
        'base_rent' => 'decimal:2',
        'extra_day_rent' => 'decimal:2',
        'overdue_rent' => 'decimal:2',
        'volume_cubic_yards' => 'decimal:2',
        'weight_limit_lbs' => 'integer',
        'tax_percentage' => 'decimal:2',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'dumpster_size_promotion');
    }
}
