<?php

namespace App\Models;

use App\Enums\TaxEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
        'type',
        'rate',
        'is_active',
    ];

    protected $casts = [
        'type' => TaxEnums::class,
        'rate' => 'float',
    ];

    // Relationships
    public function dumpsterSizes(): BelongsToMany
    {
        return $this->belongsToMany(DumpsterSize::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeFilters(Builder $query, array $filters = []): Builder
    {
        return $query->when(
            $filters,
            fn($q) => $q
                ->when(isset($filters['id']), fn($q) => $q->where('id', $filters['id']))
                ->when(isset($filters['company_id']), fn($q) => $q->where('company_id', $filters['company_id']))
                ->when(isset($filters['name']), fn($q) => $q->where('name', $filters['name']))
                ->when(isset($filters['type']), fn($q) => $q->where('type', $filters['type']))
                ->when(isset($filters['rate']), fn($q) => $q->where('rate', $filters['rate']))
                ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']))
        );
    }
}
