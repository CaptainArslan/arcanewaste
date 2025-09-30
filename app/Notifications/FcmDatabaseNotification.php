<?php

namespace App\Notifications;

use App\Events\FcmNotificationEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FcmDatabaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public bool $shouldDispatchEvent = true;

    protected string $title;

    protected string $body;

    protected array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $body, array $data, bool $shouldDispatchEvent = true)
    {
        //
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->shouldDispatchEvent = $shouldDispatchEvent;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        if ($this->shouldDispatchEvent) {
            FcmNotificationEvent::dispatch(
                $notifiable,
                $this->title,
                $this->body,
                $this->data,
            );
        }

        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
