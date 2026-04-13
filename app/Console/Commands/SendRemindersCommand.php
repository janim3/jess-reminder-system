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
        $currentTime = Carbon::now()->format('H:i');

        $assignments = Assignment::with(['contact', 'template'])->get();

        $dispatched = 0;

        foreach ($assignments as $assignment) {
            if ($this->shouldDispatch($assignment, $currentTime)) {
                $engine->dispatch($assignment);
                $dispatched++;
            }
        }

        $this->info("Dispatched {$dispatched} reminder job(s) at {$currentTime}.");
    }

    private function shouldDispatch(Assignment $assignment, string $currentTime): bool
    {
        $sendTimes = $assignment->send_times ?? [];

        if (empty($sendTimes)) {
            return false;
        }

        foreach ($sendTimes as $time) {
            if ($time === $currentTime) {
                return true;
            }
        }

        return false;
    }
}
