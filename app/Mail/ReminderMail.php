<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageContent;
    public string $contactName;

    public function __construct(string $contactName, string $messageContent)
    {
        $this->contactName    = $contactName;
        $this->messageContent = $messageContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder for ' . $this->contactName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder',
        );
    }
}
