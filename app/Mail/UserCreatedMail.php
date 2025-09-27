<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $name;
    public string $password;
    public string $email;
    public ?string $webUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $name, string $email, string $password, ?string $webUrl = null)
    {
        $this->name = $name;
        $this->password = $password;
        $this->email = $email;
        // Optional web URL (useful only if a web interface exists)
        $this->webUrl = $webUrl ?? null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎉 Welcome to " . config('app.name') . " - {$this->name}"
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account_created',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'web_url' => $this->webUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
