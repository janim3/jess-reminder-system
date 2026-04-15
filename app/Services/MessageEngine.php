<?php

namespace App\Services;

use App\Jobs\SendReminderJob;
use App\Models\Assignment;

class MessageEngine
{
    public function dispatch(Assignment $assignment): void
    {
        // Run synchronously so reminders work with cron-only hosting (no queue worker).
        SendReminderJob::dispatchSync($assignment);
    }
}
