<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class DeviceService
{
    public function registerDevice(Model $deviceable, array $deviceData): void
    {
        if (!empty($deviceData['device_id']) && !empty($deviceData['device_token']) && !empty($deviceData['device_type'])) {
            $deviceable->devices()->updateOrCreate(
                ['device_id' => $deviceData['device_id']],
                [
                    'device_token' => $deviceData['device_token'],
                    'device_type'  => $deviceData['device_type'],
                ]
            );
        }
    }
}
