<?php

namespace App\Models;

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
    public function scopeFilters($query, array $filters = [])
    {
        // Direct filters
        $directFilters = [
            'id',
            'day_of_week',
            'opens_at',
            'closes_at',
            'is_closed',
            'timeable_type',
            'timeable_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        foreach ($directFilters as $field) {
            if (isset($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        // Polymorphic filters
        if (isset($filters['company_id'])) {
            $query->whereHasMorph(
                'timeable',
                [Company::class],
                fn($q) => $q->where('id', $filters['company_id'])
            );
        }

        if (isset($filters['warehouse_id'])) {
            $query->whereHasMorph(
                'timeable',
                [Warehouse::class],
                fn($q) => $q->where('id', $filters['warehouse_id'])
            );
        }

        if (isset($filters['driver_id'])) {
            $query->whereHasMorph(
                'timeable',
                [Driver::class],
                fn($q) => $q->where('id', $filters['driver_id'])
            );
        }

        if (isset($filters['customer_id'])) {
            $query->whereHasMorph(
                'timeable',
                [Customer::class],
                fn($q) => $q->where('id', $filters['customer_id'])
            );
        }

        return $query;
    }
}
