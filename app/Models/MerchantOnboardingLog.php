<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MerchantOnboardingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'payment_method_id',
        'action',
        'source',
        'payload',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
        'payload' => 'array',
        'notes' => 'string',
    ];
}
