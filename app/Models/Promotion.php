<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enums\DiscountTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'image',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'min_order_amount',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'min_order_amount' => 'float',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'discount_value' => 'float',
        'discount_type' => DiscountTypeEnum::class,
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function dumpsterSizes(): BelongsToMany
    {
        return $this->belongsToMany(DumpsterSize::class, 'dumpster_size_promotion');
    }

    // Scopes
    public function scopeFilters(Builder $query, array $filters = []): Builder
    {
        return $query
            ->when(
                $filters['company_id'] ?? null,
                fn($q) => $q->where('company_id', $filters['company_id'])
            )
            ->when(
                $filters['search'] ?? null,
                fn($q, $search) => $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('discount_type', 'like', "%{$search}%")
                    ->orWhere('discount_value', 'like', "%{$search}%")
                    ->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%")
                    ->orWhere('min_order_amount', 'like', "%{$search}%")
                    ->orWhere('usage_limit', 'like', "%{$search}%")
                    ->orWhere('used_count', 'like', "%{$search}%")
            )
            ->when(
                $filters['is_active'] ?? null,
                fn($q) => $q->where('is_active', $filters['is_active'])
            )
            ->when(
                $filters['start_date'] ?? null,
                fn($q, $date) => $q->whereDate('start_date', '>=', $date)
            )
            ->when(
                $filters['end_date'] ?? null,
                fn($q, $date) => $q->whereDate('end_date', '<=', $date)
            )
            ->when(
                $filters['min_order_amount'] ?? null,
                fn($q, $amount) => $q->where('min_order_amount', '>=', $amount)
            )
            ->when(
                $filters['discount_type'] ?? null,
                fn($q, $type) => $q->where('discount_type', $type)
            );
    }

    // Helper methods
    public function isValid(): bool
    {
        $today = Carbon::today();

        return $this->is_active
            && (! $this->start_date || $today->gte(Carbon::parse($this->start_date)))
            && (! $this->end_date || $today->lte(Carbon::parse($this->end_date)))
            && (! $this->usage_limit || $this->used_count < $this->usage_limit);
    }

    public function canBeApplied(): bool
    {
        $today = Carbon::today();

        // Promotion must be active
        if (! $this->is_active) {
            return false;
        }

        // Must not start in the future
        if ($this->start_date && $today->lt(Carbon::parse($this->start_date))) {
            return false;
        }

        // Must not be expired
        if ($this->end_date && $today->gt(Carbon::parse($this->end_date))) {
            return false;
        }

        // Usage limit check
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }
}
