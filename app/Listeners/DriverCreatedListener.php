<?php

namespace App\Listeners;

use App\Events\DriverCreatedEvent;
use App\Mail\UserCreatedMail;
use App\Notifications\FcmDatabaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DriverCreatedListener
{
    public function handle(DriverCreatedEvent $event): void
    {
        $driver = $event->driver;
        $password = $event->password;
        $driver->notify(new FcmDatabaseNotification(
            'Driver Created',
            'Driver created successfully',
            [
                'driver_id' => $driver->id,
                'driver_name' => $driver->full_name,
                'driver_email' => $driver->email,
                'driver_phone' => $driver->phone,
            ],
            false
        ));

        try {
            Mail::to($driver->email)->send(new UserCreatedMail($driver->full_name, $driver->email, $password, url('/')));
        } catch (\Throwable $th) {
            Log::error('Driver Created Mail Failed', [
                'driver_id' => $driver->id,
                'driver_email' => $driver->email,
                'error' => $th->getMessage(),
            ]);
        }
    }
}
