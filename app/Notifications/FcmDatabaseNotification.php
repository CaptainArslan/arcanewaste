<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Events\FcmNotificationEvent;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FcmDatabaseNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $body;
    protected array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $body, array $data)
    {
        //
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
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
        FcmNotificationEvent::dispatch(
            $notifiable,
            $this->title,
            $this->body,
            $this->data,
        );

        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
