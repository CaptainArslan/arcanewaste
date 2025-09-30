<?php

namespace App\Listeners;

use App\Events\CustomerCreatedEvent;
use App\Mail\UserCreatedMail;
use App\Notifications\FcmDatabaseNotification;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerCreatedListener implements ShouldDispatchAfterCommit, ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CustomerCreatedEvent $event): void
    {
        $customer = $event->customer;
        $password = $event->password;
        $customer->notify(new FcmDatabaseNotification(
            'Customer Created',
            'Customer created successfully',
            [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
            ],
            false
        ));

        try {
            Mail::to($customer->email)->send(new UserCreatedMail($customer->full_name, $customer->email, $password, url('/')));
        } catch (\Throwable $th) {
            Log::error('Customer Created Mail Failed', [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'error' => $th->getMessage(),
            ]);
        }
    }
}
