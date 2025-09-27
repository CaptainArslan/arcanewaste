<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Customer extends Authenticatable implements JWTSubject
{
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
        return $this->belongsToMany(Company::class)
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

    // Accessors & Mutators
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Storage::disk('s3')->url($value) : null,
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucwords($value),
            set: fn(string $value) => strtolower(trim($value)),
        );
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
