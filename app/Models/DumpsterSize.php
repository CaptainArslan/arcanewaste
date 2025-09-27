<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DumpsterSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'image',
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
        'min_rental_days' => 'integer',
        'max_rental_days' => 'integer',
        'base_rent' => 'float',
        'extra_day_rent' => 'float',
        'overdue_rent' => 'float',
        'volume_cubic_yards' => 'float',
        'weight_limit_lbs' => 'integer',
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

    // Accessors and Mutators
    public function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value
                ? (filter_var($value, FILTER_VALIDATE_URL)
                    ? $value
                    : Storage::disk('s3')->url($value))
                : null,
        );
    }

    // Scopes
    public function scopeFilters(Builder $query, array $filters = []): Builder
    {
        return $query->when($filters, function ($query, $filters) {
            return $query->when(isset($filters['id']), fn($q) => $q->where('id', $filters['id']))
                ->when(isset($filters['company_id']), fn($q) => $q->where('company_id', $filters['company_id']))
                ->when(isset($filters['name']), fn($q) => $q->where('name', 'like', '%' . $filters['name'] . '%'))
                ->when(isset($filters['code']), fn($q) => $q->where('code', 'like', '%' . $filters['code'] . '%'))
                ->when(isset($filters['description']), fn($q) => $q->where('description', 'like', '%' . $filters['description'] . '%'))
                ->when(isset($filters['min_rental_days']), fn($q) => $q->where('min_rental_days', '<=', $filters['min_rental_days']))
                ->when(isset($filters['max_rental_days']), fn($q) => $q->where('max_rental_days', '>=', $filters['max_rental_days']))
                ->when(isset($filters['base_rent']), fn($q) => $q->where('base_rent', '>=', $filters['base_rent']))
                ->when(isset($filters['extra_day_rent']), fn($q) => $q->where('extra_day_rent', '>=', $filters['extra_day_rent']))
                ->when(isset($filters['overdue_rent']), fn($q) => $q->where('overdue_rent', '>=', $filters['overdue_rent']))
                ->when(isset($filters['volume_cubic_yards']), fn($q) => $q->where('volume_cubic_yards', '>=', $filters['volume_cubic_yards']))
                ->when(isset($filters['weight_limit_lbs']), fn($q) => $q->where('weight_limit_lbs', '>=', $filters['weight_limit_lbs']))
                ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']));
        });
    }
}
