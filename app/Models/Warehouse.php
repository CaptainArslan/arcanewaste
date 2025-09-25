<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Traits\HasAddresses;
use App\Traits\HasDocuments;

class Warehouse extends Model
{
    use HasFactory;
    use HasAddresses, HasDocuments;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'type',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        // 'is_active' => 'boolean',
    ];

    protected $hidden = [
        //
    ];

    protected $appends = [
        //
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function defaultAddress(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_primary', true);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function timings(): MorphMany
    {
        return $this->morphMany(Timing::class, 'timeable');
    }

    public function dumpsters(): HasMany
    {
        return $this->hasMany(Dumpster::class);
    }

    // Scopes
    public function scopeFilters($query, $filters)
    {
        return $query->when(isset($filters['name']), fn($q) => $q->where('name', 'like', '%' . $filters['name'] . '%'))
            ->when(isset($filters['code']), fn($q) => $q->where('code', 'like', '%' . $filters['code'] . '%'))
            ->when(isset($filters['type']), fn($q) => $q->where('type', 'like', '%' . $filters['type'] . '%'))
            ->when(isset($filters['capacity']), fn($q) => $q->where('capacity', 'like', '%' . $filters['capacity'] . '%'))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']));
    }
}
