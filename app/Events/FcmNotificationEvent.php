<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FcmNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;
    public $body;
    public $data;
    public $modelType;
    public $modelId;
    public $deviceTokens;

    /**
     * Create a new event instance.
     */
    public function __construct(array $deviceTokens = [], string $title, string $body, array $data = [], string $modelType, int $modelId)
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->modelType = $modelType;
        $this->modelId = $modelId;
        $this->deviceTokens = $deviceTokens;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('fcm-notification-' . $this->modelType . '-' . $this->modelId . '-channel'),
        ];
    }
}
