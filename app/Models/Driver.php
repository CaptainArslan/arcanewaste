<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Driver extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
    public function generalSettings(): MorphMany
    {
        return $this->morphMany(GeneralSetting::class, 'settingable');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function defaultAddress(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_primary', true);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function latestLocation(): MorphOne
    {
        return $this->morphOne(LatestLocation::class, 'locatable');
    }

    public function emergencyContacts() : MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable')->where('type', 'emergency');
    }
}
