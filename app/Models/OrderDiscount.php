<?php

namespace App\Models;

use App\Enums\DiscountTypeEnum;
use Illuminate\Database\Eloquent\Model;

class OrderDiscount extends Model
{
    protected $fillable = [
        'order_id',
        'promotion_id',
        'discount_type',
        'discount_value',
    ];

    protected $casts = [
        'discount_type' => DiscountTypeEnum::class,
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
