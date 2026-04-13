<?php

namespace Tests\Feature;

use App\Jobs\SendReminderJob;
use App\Models\Assignment;
use App\Models\Contact;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduler_dispatches_job_for_matching_time(): void
    {
        Queue::fake();

        Carbon::setTestNow(Carbon::parse('2026-01-01 09:00:00'));

        $contact  = Contact::factory()->create();
        $template = Template::factory()->create();

        Assignment::factory()->create([
            'contact_id'     => $contact->id,
            'template_id'    => $template->id,
            'frequency_type' => 'daily_once',
            'send_times'     => ['09:00'],
            'channel'        => 'sms',
        ]);

        $this->artisan('reminders:send')->assertSuccessful();

        Queue::assertPushed(SendReminderJob::class);

        Carbon::setTestNow();
    }

    public function test_scheduler_does_not_dispatch_job_for_non_matching_time(): void
    {
        Queue::fake();

        Carbon::setTestNow(Carbon::parse('2026-01-01 10:00:00'));

        $contact  = Contact::factory()->create();
        $template = Template::factory()->create();

        Assignment::factory()->create([
            'contact_id'     => $contact->id,
            'template_id'    => $template->id,
            'frequency_type' => 'daily_once',
            'send_times'     => ['09:00'],
            'channel'        => 'sms',
        ]);

        $this->artisan('reminders:send')->assertSuccessful();

        Queue::assertNothingPushed();

        Carbon::setTestNow();
    }
}
