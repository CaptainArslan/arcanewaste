<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dumpster extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'dumpster_size_id',
        'warehouse_id',
        'serial_number',
        'status',
        'last_service_date',
        'next_service_due',
        'notes',
        'is_available',
        'is_active',
    ];

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

    // public function orders()
    // {
    //     return $this->hasMany(Order::class);
    // }

    // Scope example: only available dumpsters
    public function scopeAvailable($query): Builder
    {
        return $query->where('status', 'available');
    }
}
