<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'type',
        'percentage',
        'is_active',
    ];

    protected $casts = [
        'type' => 'string',
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
