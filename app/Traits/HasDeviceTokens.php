<?php

namespace App\Traits;

use App\Interfaces\NotifiableViaFcm;
use App\Models\DeviceToken;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDeviceTokens
{
    public function devices(): MorphMany
    {
        return $this->morphMany(DeviceToken::class, 'deviceable');
    }

    public function getDeviceTokens(): array
    {
        return $this->devices()
            ->pluck('device_token')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
}
