<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\HasAddresses;
use App\Traits\HasDocuments;

class Company extends Authenticatable implements JWTSubject
{
    use HasFactory, HasSlug, Notifiable, SoftDeletes;
    use HasAddresses, HasDocuments;

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
        //
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

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // Relationships
    public function devices(): MorphMany
    {
        return $this->morphMany(DeviceToken::class, 'deviceable');
    }

    public function paymentOptions(): HasMany
    {
        return $this->hasMany(PaymentOption::class, 'company_id');
    }

    public function companyPaymentMethods(): HasMany
    {
        return $this->hasMany(CompantPaymentMethod::class, 'company_id');
    }

    public function merchantOnboardingLogs(): HasMany
    {
        return $this->hasMany(MerchantOnboardingLog::class, 'company_id');
    }

    public function generalSettings(): MorphMany
    {
        return $this->morphMany(GeneralSetting::class, 'settingable');
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function timings(): MorphMany
    {
        return $this->morphMany(Timing::class, 'timeable');
    }

    public function holidays(): MorphMany
    {
        return $this->morphMany(Holiday::class, 'holidayable');
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class, 'company_id');
    }

    public function dumpsterSizes(): HasMany
    {
        return $this->hasMany(DumpsterSize::class, 'company_id');
    }

    public function dumpsters(): HasMany
    {
        return $this->hasMany(Dumpster::class, 'company_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'company_customer')
            ->withPivot('is_active', 'is_delinquent', 'delinquent_days')
            ->withTimestamps();
    }

    // Accessors & Mutators
    protected function logo(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Storage::disk('s3')->url($value),
        );
    }

    // Helper methods
    public function getDeviceTokens(): array
    {
        return $this->devices()->pluck('device_token')->toArray();
    }
}
