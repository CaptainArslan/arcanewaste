<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class DeviceService
{
    public function registerDevice(Model $deviceable, array $deviceData): ?Model
    {
        if (! empty($deviceData['device_id']) && ! empty($deviceData['device_token']) && ! empty($deviceData['device_type'])) {
            return $deviceable->devices()->updateOrCreate(
                ['device_id' => $deviceData['device_id']],
                [
                    'device_token' => $deviceData['device_token'],
                    'device_type' => $deviceData['device_type'],
                ]
            );
        }

        return null;
    }

    public function unregisterDevice(Model $deviceable, array $deviceData): bool
    {
        if (! empty($deviceData['device_id'])) {
            return (bool) $deviceable->devices()->where('device_id', $deviceData['device_id'])->delete();
        }

        return false;
    }
}
