<?php

namespace App\Models;

use Carbon\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Customer extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
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

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_customer')
            ->withPivot('payment_option_id')
            ->withTimestamps();
    }

    public function paymentOptionForCompany($companyId)
    {
        return $this->companies()
            ->where('company_id', $companyId)
            ->first()?->pivot->payment_option_id;
    }
}
