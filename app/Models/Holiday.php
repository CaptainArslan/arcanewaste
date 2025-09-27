<?php

namespace App\Models;

use App\Enums\HolidayApprovalStatusEnums;
use App\Models\Driver;
use App\Models\Company;
use App\Enums\RecurrenceTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'holidayable_id',
        'holidayable_type',
        'name',
        'date',
        'recurrence_type',
        'day_of_week',
        'month_day',
        'reason',
        'is_approved',
        'is_active',
    ];

    protected $casts = [
        'recurrence_type' => RecurrenceTypeEnum::class,
        'is_approved' => HolidayApprovalStatusEnums::class,
    ];

    // Relationships
    public function company(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeFilters(Builder $query, array $filters = []): Builder
    {
        return $query
            ->when(isset($filters['id']), fn($q) => $q->where('id', $filters['id']))
            ->when(
                isset($filters['company_id']),
                fn($q) =>
                $q->where('holidayable_type', Company::class)
                    ->where('holidayable_id', $filters['company_id'])
            )
            ->when(
                isset($filters['driver_id']),
                fn($q) =>
                $q->where('holidayable_type', Driver::class)
                    ->where('holidayable_id', $filters['driver_id'])
            )
            ->when(isset($filters['holidayable_id']), fn($q) => $q->where('holidayable_id', $filters['holidayable_id']))
            ->when(isset($filters['holidayable_type']), fn($q) => $q->where('holidayable_type', $filters['holidayable_type']))
            ->when(isset($filters['name']), fn($q) => $q->where('name', 'like', '%' . $filters['name'] . '%'))
            ->when(isset($filters['date']), fn($q) => $q->whereDate('date', $filters['date']))
            ->when(isset($filters['from_date']), fn($q) => $q->whereDate('date', '>=', $filters['from_date']))
            ->when(isset($filters['to_date']), fn($q) => $q->whereDate('date', '<=', $filters['to_date']))
            ->when(isset($filters['recurrence_type']), fn($q) => $q->where('recurrence_type', $filters['recurrence_type']))
            ->when(isset($filters['day_of_week']), fn($q) => $q->where('day_of_week', $filters['day_of_week']))
            ->when(isset($filters['month_day']), fn($q) => $q->where('month_day', $filters['month_day']))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', (bool) $filters['is_active']))
            ->when(isset($filters['is_approved']), fn($q) => $q->where('is_approved', (bool) $filters['is_approved']));
    }
}
