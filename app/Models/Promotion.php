<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'dumpster_size_id',
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

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function dumpsterSize(): BelongsTo
    {
        return $this->belongsTo(DumpsterSize::class);
    }

    // Helper methods
    public function isValid(): bool
    {
        $today = now()->toDateString();

        if (!$this->is_active) return false;
        if ($this->start_date && $today < $this->start_date) return false;
        if ($this->end_date && $today > $this->end_date) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;

        return true;
    }
}
