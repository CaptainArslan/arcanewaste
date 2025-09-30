<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'percentage',
        'description',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'name' => 'string',
        'type' => 'string',
        'percentage' => 'float',
        'description' => 'string',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeFilters(Builder $query, $filters = []): Builder
    {
        return $query->when($filters, function ($query, $filters) {
            return $query
                ->when(isset($filters['id']), fn ($q) => $q->where('id', $filters['id']))
                ->when(isset($filters['name']), fn ($q) => $q->where('name', 'like', '%'.$filters['name'].'%'))
                ->when(isset($filters['type']), fn ($q) => $q->where('type', $filters['type']))
                ->when(isset($filters['percentage']), fn ($q) => $q->where('percentage', $filters['percentage']))
                ->when(isset($filters['description']), fn ($q) => $q->where('description', 'like', '%'.$filters['description'].'%'))
                ->when(isset($filters['is_active']), fn ($q) => $q->where('is_active', $filters['is_active']))
                ->when(isset($filters['company_id']), fn ($q) => $q->where('company_id', $filters['company_id']));
        });
    }
}
