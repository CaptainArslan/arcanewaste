<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class FcmNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $model;
    public array $deviceTokens;
    public string $title;
    public string $body;
    public array $data;

    public function __construct(Model $model, string $title, string $body, array $data = [])
    {
        $this->deviceTokens = method_exists($model, 'getDeviceTokens')
            ? $model->getDeviceTokens()
            : [];

        $this->model = $model;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        $modelType = class_basename($this->model);
        $modelId   = $this->model->getKey();

        return [
            new PrivateChannel("fcm-notification-{$modelType}-{$modelId}-channel"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'fcm.notification';
    }

    public function broadcastWith(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
