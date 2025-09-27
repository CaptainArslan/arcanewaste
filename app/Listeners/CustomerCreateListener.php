<?php

namespace App\Listeners;

use App\Mail\CustomerCreatedMail;
use Illuminate\Support\Facades\Log;
use App\Events\CustomerCreatedEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\FcmDatabaseNotification;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

class CustomerCreateListener implements ShouldQueue, ShouldDispatchAfterCommit
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
            ]
        ));

        try {
            Mail::to($customer->email)->send(new CustomerCreatedMail($customer, $password));
        } catch (\Throwable $th) {
            Log::error('Customer Created Mail Failed', [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'error' => $th->getMessage(),
            ]);
        }
    }
}
