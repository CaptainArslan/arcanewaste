<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'reference_order_id'
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function dumpster()
    {
        return $this->belongsTo(Dumpster::class);
    }
    public function dumpsterSize()
    {
        return $this->belongsTo(DumpsterSize::class);
    }
    public function wasteType()
    {
        return $this->belongsTo(WasteType::class);
    }

    public function timings()
    {
        return $this->hasOne(OrderTiming::class);
    }
    public function pricing()
    {
        return $this->hasOne(OrderPricing::class);
    }
    public function discounts()
    {
        return $this->hasMany(OrderDiscount::class);
    }
    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }
    public function address()
    {
        return $this->hasOne(OrderAddress::class);
    }
}
