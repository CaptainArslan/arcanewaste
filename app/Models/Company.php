<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\PaymentMethod;
use App\Models\GeneralSetting;
use App\Models\MerchantOnboardingLog;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'customer_panel_url',
        'logo',
        'description',
        'address',
        'phone',
        'email',
        'password',
        'website',
        'onboarding_status',
        'finix_identity_id',
        'finix_merchant_id',
        'finix_onboarding_form_id',
        'finix_onboarding_url',
        'finix_onboarding_url_expired_at',
        'finix_onboarding_status',
        'finix_onboarding_notes',
        'finix_onboarding_completed_at',
        'is_active',
    ];

    protected $casts = [
        'finix_onboarding_url_expired_at' => 'datetime',
        'finix_onboarding_completed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'finix_identity_id',
        'finix_merchant_id',
        'finix_onboarding_form_id',
        'finix_onboarding_url',
        'finix_onboarding_url_expired_at',
        'finix_onboarding_status',
        'finix_onboarding_notes',
        'finix_onboarding_completed_at',
        'remember_token',
    ];

    protected $appends = [
        'logo_url',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'exp' => Carbon::now()->addDays(30)->timestamp,
        ];
    }

    // Relationships
    public function generalSettings(): MorphMany
    {
        return $this->morphMany(GeneralSetting::class, 'settingable');
    }

    public function defaultAddress(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_primary', true);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class, 'company_id');
    }

    public function merchantOnboardingLogs(): HasMany
    {
        return $this->hasMany(MerchantOnboardingLog::class, 'company_id');
    }

    public function timings(): MorphMany
    {
        return $this->morphMany(Timing::class, 'timeable');
    }

    public function dumpsterSizes(): HasMany
    {
        return $this->hasMany(DumpsterSize::class, 'company_id');
    }

    public function paymentOptions(): HasMany
    {
        return $this->hasMany(PaymentOption::class, 'company_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'company_customer')
            ->withPivot('payment_option_id')
            ->withTimestamps();
    }
}
