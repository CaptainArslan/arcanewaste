<?php

namespace App\Models;

use App\Traits\HasAddresses;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use HasAddresses;
    use HasFactory;
    use HasSlug;
    use Notifiable;

    protected $fillable = [
        'full_name',
        'slug',
        'email',
        'phone',
        'image',
        'system_generated_password',
        'gender',
        'dob',
        'password',
    ];

    protected $casts = [
        'system_generated_password' => 'boolean',
        'dob' => 'date',
        'email_verified_at' => 'datetime',
        'remember_token' => 'string',
    ];

    protected $hidden = [
        'password',
        'remember_token',
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
            ->generateSlugsFrom('full_name')
            ->saveSlugsTo('slug');
    }

    // Relationships
    public function generalSettings(): MorphMany
    {
        return $this->morphMany(GeneralSetting::class, 'settingable');
    }

    public function devices(): MorphMany
    {
        return $this->morphMany(DeviceToken::class, 'deviceable');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function defaultAddress(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_primary', true);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_customer')
            ->withPivot('is_active', 'is_delinquent', 'delinquent_days')
            ->withTimestamps();
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function latestLocation(): MorphOne
    {
        return $this->morphOne(LatestLocation::class, 'locatable');
    }

    public function allContacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function emergencyContacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable')->where('type', 'emergency');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Accessors & Mutators
    public function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value
                ? (filter_var($value, FILTER_VALIDATE_URL)
                    ? $value
                    : Storage::disk('s3')->url($value))
                : null,
        );
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords($value),
            set: fn (string $value) => strtolower(trim($value)),
        );
    }

    // scopes
    public function scopeFilters(Builder $query, array $filters = [], ?int $companyId = null): Builder
    {
        // Join the pivot table to filter company-specific fields
        if ($companyId) {
            $query->join('company_customer', function ($join) use ($companyId) {
                $join->on('customers.id', '=', 'company_customer.customer_id')
                    ->where('company_customer.company_id', $companyId);
            });
        }

        return $query
            // Customer table filters
            ->when(! empty($filters['full_name']), fn ($q) => $q->where('full_name', 'like', '%'.$filters['full_name'].'%'))
            ->when(! empty($filters['email']), fn ($q) => $q->where('email', 'like', '%'.$filters['email'].'%'))
            ->when(! empty($filters['phone']), fn ($q) => $q->where('phone', 'like', '%'.$filters['phone'].'%'))
            ->when(! empty($filters['gender']), fn ($q) => $q->where('gender', $filters['gender']))
            ->when(! empty($filters['dob']), function ($q) use ($filters) {
                if (is_array($filters['dob']) && isset($filters['dob']['from'], $filters['dob']['to'])) {
                    $q->whereBetween('dob', [$filters['dob']['from'], $filters['dob']['to']]);
                } else {
                    $q->where('dob', $filters['dob']);
                }
            })

            // Pivot table filters
            ->when(isset($filters['is_active']), fn ($q) => $q->where('company_customer.is_active', $filters['is_active']))
            ->when(isset($filters['is_delinquent']), fn ($q) => $q->where('company_customer.is_delinquent', $filters['is_delinquent']))
            ->when(! empty($filters['delinquent_days']), function ($q) use ($filters) {
                if (is_array($filters['delinquent_days']) && isset($filters['delinquent_days']['min'], $filters['delinquent_days']['max'])) {
                    $q->whereBetween('company_customer.delinquent_days', [$filters['delinquent_days']['min'], $filters['delinquent_days']['max']]);
                } else {
                    $q->where('company_customer.delinquent_days', $filters['delinquent_days']);
                }
            });
    }

    // Helper methods
    public function getDeviceTokens(): array
    {
        return $this->devices()->pluck('device_token')->toArray();
    }

    public function isActiveForCompany(Company $company): bool
    {
        return $this->companies()
            ->where('company_id', $company->id)
            ->wherePivot('is_active', true)
            ->exists();
    }

    public function isDelinquentForCompany(Company $company): bool
    {
        return $this->companies()
            ->where('company_id', $company->id)
            ->wherePivot('is_delinquent', true)
            ->exists();
    }

    public function getDelinquentDaysForCompany(Company $company): int
    {
        $pivot = $this->companies()
            ->where('company_id', $company->id)
            ->first();

        return $pivot ? $pivot->pivot->delinquent_days : 0;
    }
}
