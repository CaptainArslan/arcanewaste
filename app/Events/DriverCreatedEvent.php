<?php

namespace App\Events;

use App\Models\Driver;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driver;

    public $password;

    public function __construct(Driver $driver, string $password)
    {
        $this->driver = $driver;
        $this->password = $password;
    }
}
