<?php

namespace App\Services;

use App\Jobs\SendReminderJob;
use App\Models\Assignment;

class MessageEngine
{
    public function dispatch(Assignment $assignment): void
    {
        SendReminderJob::dispatch($assignment);
    }
}
