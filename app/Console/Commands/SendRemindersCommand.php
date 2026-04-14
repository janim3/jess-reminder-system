<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Services\MessageEngine;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRemindersCommand extends Command
{
    protected $signature   = 'reminders:send';
    protected $description = 'Dispatch reminder jobs for assignments that match the current time';

    public function handle(MessageEngine $engine): void
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        $assignments = Assignment::with(['contact', 'template'])->get();

        $dispatched = 0;

        foreach ($assignments as $assignment) {
            if ($this->shouldDispatch($assignment, $now)) {
                $engine->dispatch($assignment);
                $dispatched++;
            }
        }

        $this->info("Dispatched {$dispatched} reminder job(s) at {$currentTime}.");
    }

    private function shouldDispatch(Assignment $assignment, Carbon $now): bool
    {
        return $assignment->shouldSendNow($now);
    }
}
