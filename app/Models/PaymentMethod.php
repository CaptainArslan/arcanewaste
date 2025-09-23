<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'logo_url',
        'website_url',
        'supported_countries',
        'supported_currencies',
        'supported_payment_types',
        'transaction_fee_percentage',
        'transaction_fee_fixed',
        'monthly_fee',
        'setup_fee',
        'min_transaction_amount',
        'max_transaction_amount',
        'api_configuration',
        'onboarding_requirements',
        'features',
        'status',
        'is_popular',
        'requires_merchant_onboarding',
        'sort_order',
    ];

    protected $casts = [
        'supported_countries' => 'array',
        'supported_currencies' => 'array',
        'supported_payment_types' => 'array',
        'transaction_fee_percentage' => 'decimal:4',
        'transaction_fee_fixed' => 'integer',
        'monthly_fee' => 'integer',
        'setup_fee' => 'integer',
        'min_transaction_amount' => 'integer',
        'max_transaction_amount' => 'integer',
        'api_configuration' => 'array',
        'onboarding_requirements' => 'array',
        'features' => 'array',
        'is_popular' => 'boolean',
        'requires_merchant_onboarding' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getLogoUrlAttribute($value)
    {
        return $value ? asset('storage/'.$value) : null;
    }

    public function getFormattedTransactionFeeAttribute()
    {
        $percentage = $this->transaction_fee_percentage * 100;
        $fixed = $this->transaction_fee_fixed / 100; // Convert cents to dollars

        if ($percentage > 0 && $fixed > 0) {
            return "{$percentage}% + $".number_format($fixed, 2);
        } elseif ($percentage > 0) {
            return "{$percentage}%";
        } elseif ($fixed > 0) {
            return '$'.number_format($fixed, 2);
        }

        return 'Free';
    }

    public function getFormattedMonthlyFeeAttribute()
    {
        $fee = $this->monthly_fee / 100; // Convert cents to dollars

        return $fee > 0 ? '$'.number_format($fee, 2).'/month' : 'Free';
    }

    public function getFormattedSetupFeeAttribute()
    {
        $fee = $this->setup_fee / 100; // Convert cents to dollars

        return $fee > 0 ? '$'.number_format($fee, 2) : 'Free';
    }

    // Methods
    public function isAvailableInCountry($countryCode)
    {
        if (empty($this->supported_countries)) {
            return true; // If no countries specified, assume global
        }

        return in_array($countryCode, $this->supported_countries);
    }

    public function isAvailableInCurrency($currencyCode)
    {
        if (empty($this->supported_currencies)) {
            return true; // If no currencies specified, assume all supported
        }

        return in_array($currencyCode, $this->supported_currencies);
    }

    public function supportsPaymentType($paymentType)
    {
        if (empty($this->supported_payment_types)) {
            return true; // If no types specified, assume all supported
        }

        return in_array($paymentType, $this->supported_payment_types);
    }

    public function hasFeature($feature)
    {
        if (empty($this->features)) {
            return false;
        }

        return in_array($feature, $this->features);
    }

    public function getOnboardingRequirements()
    {
        return $this->onboarding_requirements ?? [];
    }

    public function getApiConfiguration()
    {
        return $this->api_configuration ?? [];
    }
}
