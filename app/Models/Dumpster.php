<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dumpster extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'company_id',
        'dumpster_size_id',
        'warehouse_id',
        'name',
        'slug',
        'serial_number',
        'status',
        'image',
        'last_service_date',
        'next_service_due',
        'notes',
        'is_available',
        'is_active',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(DumpsterSize::class, 'dumpster_size_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
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

    // Scope example: only available dumpsters
    public function scopeFilters($query, $filters = []): Builder
    {
        return $query->when(
            $filters,
            fn($q) => $q->when(isset($filters['id']), fn($q) => $q->where('id', $filters['id']))
                ->when(isset($filters['company_id']), fn($q) => $q->where('company_id', $filters['company_id']))
                ->when(isset($filters['dumpster_size_id']), fn($q) => $q->where('dumpster_size_id', $filters['dumpster_size_id']))
                ->when(isset($filters['warehouse_id']), fn($q) => $q->where('warehouse_id', $filters['warehouse_id']))
                ->when(isset($filters['name']), fn($q) => $q->where('name', $filters['name']))
                ->when(isset($filters['slug']), fn($q) => $q->where('slug', $filters['slug']))
                ->when(isset($filters['serial_number']), fn($q) => $q->where('serial_number', $filters['serial_number']))
                ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
                ->when(isset($filters['last_service_date']), fn($q) => $q->where('last_service_date', $filters['last_service_date']))
                ->when(isset($filters['next_service_due']), fn($q) => $q->where('next_service_due', $filters['next_service_due']))
                ->when(isset($filters['notes']), fn($q) => $q->where('notes', $filters['notes']))
                ->when(isset($filters['is_available']), fn($q) => $q->where('is_available', $filters['is_available']))
                ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']))
        );
    }

    public function scopeAvailable($query): Builder
    {
        return $query->where('status', 'available');
    }
}
