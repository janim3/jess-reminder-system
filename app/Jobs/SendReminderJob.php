<?php

namespace App\Jobs;

use App\Mail\ReminderMail;
use App\Models\Assignment;
use App\Services\SmsServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendReminderJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly Assignment $assignment)
    {
    }

    public function handle(SmsServiceInterface $smsService): void
    {
        $assignment = $this->assignment;
        $contact    = $assignment->contact;
        $template   = $assignment->template;
        $message    = $template->parseContent($contact);

        if (in_array($assignment->channel, ['sms', 'both'])) {
            $smsService->send($contact->phone_number, $message);
        }

        if (in_array($assignment->channel, ['email', 'both'])) {
            if ($contact->email) {
                Mail::to($contact->email)->send(new ReminderMail($contact->name, $message));
            }
        }
    }
}
