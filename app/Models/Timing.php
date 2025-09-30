<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'hours',
    ];

    protected $casts = [
        'opens_at' => 'datetime:H:i',
        'closes_at' => 'datetime:H:i',
        'is_closed' => 'boolean',
        'hours' => 'integer',
    ];

    // Relationships
    public function timeable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeFilters(Builder $query, array $filters = []): Builder
    {
        return $query->when(
            $filters,
            fn ($q) => $q
                // Direct fields
                ->when(isset($filters['id']), fn ($q) => $q->where('id', $filters['id']))
                ->when(isset($filters['day_of_week']), fn ($q) => $q->where('day_of_week', $filters['day_of_week']))
                ->when(isset($filters['opens_at']), fn ($q) => $q->where('opens_at', $filters['opens_at']))
                ->when(isset($filters['closes_at']), fn ($q) => $q->where('closes_at', $filters['closes_at']))
                ->when(isset($filters['is_closed']), fn ($q) => $q->where('is_closed', $filters['is_closed']))
                ->when(isset($filters['timeable_type']), fn ($q) => $q->where('timeable_type', $filters['timeable_type']))
                ->when(isset($filters['timeable_id']), fn ($q) => $q->where('timeable_id', $filters['timeable_id']))
                ->when(isset($filters['created_at']), fn ($q) => $q->where('created_at', $filters['created_at']))
                ->when(isset($filters['updated_at']), fn ($q) => $q->where('updated_at', $filters['updated_at']))
                ->when(isset($filters['deleted_at']), fn ($q) => $q->where('deleted_at', $filters['deleted_at']))

                // Polymorphic relations
                ->when(isset($filters['company_id']), fn ($q) => $q->whereHasMorph('timeable', [Company::class], fn ($q) => $q->where('id', $filters['company_id'])))
                ->when(isset($filters['warehouse_id']), fn ($q) => $q->whereHasMorph('timeable', [Warehouse::class], fn ($q) => $q->where('id', $filters['warehouse_id'])))
                ->when(isset($filters['driver_id']), fn ($q) => $q->whereHasMorph('timeable', [Driver::class], fn ($q) => $q->where('id', $filters['driver_id'])))
                ->when(isset($filters['customer_id']), fn ($q) => $q->whereHasMorph('timeable', [Customer::class], fn ($q) => $q->where('id', $filters['customer_id'])))
        );
    }
}
