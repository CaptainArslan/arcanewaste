<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'uuid',
        'company_id',
        'customer_id',
        'dumpster_id',
        'dumpster_size_id',
        'waste_type_id',
        'status',
        'return_requested',
        'is_early_return',
        'is_job_shifted',
        'order_type',
        'reference_order_id',
    ];

    // Relationships
    public function company() : BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer() : BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function dumpster() : BelongsTo
    {
        return $this->belongsTo(Dumpster::class);
    }

    public function dumpsterSize() : BelongsTo
    {
        return $this->belongsTo(DumpsterSize::class);
    }

    public function wasteType() : BelongsTo
    {
        return $this->belongsTo(WasteType::class);
    }

    public function timings() : HasOne
    {
        return $this->hasOne(OrderTiming::class);
    }

    public function pricing() : HasOne
    {
        return $this->hasOne(OrderPricing::class);
    }

    public function discounts() : HasMany
    {
        return $this->hasMany(OrderDiscount::class);
    }

    public function payments() : HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function address() : HasOne
    {
        return $this->hasOne(OrderAddress::class);
    }
}
