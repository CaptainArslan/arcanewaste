<?php

namespace App\Listeners;

use App\Events\CompanySetupSuccessfullyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CompanySetupSuccessfullyListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CompanySetupSuccessfullyEvent $event): void
    {
        //
    }
}
