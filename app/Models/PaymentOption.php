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
        'is_active' => 'boolean',
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
            return $query->when($filters['id'] ?? false, fn($q, $id) => $q->where('id',  $id))
                ->when($filters['name'] ?? false, fn($q, $name) => $q->where('name', 'like', '%' . $name . '%'))
                ->when($filters['type'] ?? false, fn($q, $type) => $q->where('type', $type))
                ->when($filters['percentage'] ?? false, fn($q, $percentage) => $q->where('percentage', $percentage))
                ->when($filters['description'] ?? false, fn($q, $description) => $q->where('description', 'like', '%' . $description . '%'))
                ->when($filters['is_active'] ?? false, fn($q, $is_active) => $q->where('is_active', $is_active))
                ->when($filters['company_id'] ?? false, fn($q, $company_id) => $q->where('company_id', $company_id));
        });
    }
}
