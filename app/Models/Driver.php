<?php

namespace App\Models;

use App\Traits\HasAddresses;
use App\Traits\HasDocuments;
use App\Traits\HasEmergencyContacts;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Driver extends Authenticatable implements JWTSubject
{
    use HasAddresses;
    use HasDocuments;
    use HasEmergencyContacts;
    use HasFactory;
    use Notifiable;

    public const DEFAULT_DUTY_HOURS = 8;

    public const DEFAULT_HOURLY_RATE = 5;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'dob',
        'gender',
        'image',
        'license_number',
        'license_expires_at',
        'identity_document',
        'identity_expires_at',
    ];

    protected $casts = [
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

    // Relationships
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_driver')
            ->withPivot('is_active', 'hourly_rate', 'duty_hours', 'employment_type', 'hired_at', 'terminated_at')
            ->withTimestamps();
    }

    public function generalSettings(): MorphMany
    {
        return $this->morphMany(GeneralSetting::class, 'settingable');
    }

    public function latestLocation(): MorphOne
    {
        return $this->morphOne(LatestLocation::class, 'locatable');
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

    // Scopes
    public function scopeFilters(Builder $query, $filters = []): Builder
    {
        return $query->when(isset($filters['company_id']), fn ($q) => $q->whereHas('companies', fn ($q) => $q->where('company_id', $filters['company_id'])))
            ->when(isset($filters['full_name']), fn ($q) => $q->where('name', 'like', '%'.$filters['full_name'].'%'))
            ->when(isset($filters['email']), fn ($q) => $q->where('email', 'like', '%'.$filters['email'].'%'))
            ->when(isset($filters['phone']), fn ($q) => $q->where('phone', 'like', '%'.$filters['phone'].'%'))
            ->when(isset($filters['gender']), fn ($q) => $q->where('gender', $filters['gender']))
            ->when(isset($filters['dob']), function ($q) use ($filters) {
                if (is_array($filters['dob']) && isset($filters['dob']['from'], $filters['dob']['to'])) {
                    $q->whereBetween('dob', [$filters['dob']['from'], $filters['dob']['to']]);
                } else {
                    $q->where('dob', $filters['dob']);
                }
            });
    }
}
