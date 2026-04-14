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

    public function test_scheduler_dispatches_job_for_advanced_weekly_rule(): void
    {
        Queue::fake();

        Carbon::setTestNow(Carbon::parse('2026-01-05 09:00:00', 'UTC'));

        $contact  = Contact::factory()->create();
        $template = Template::factory()->create();

        Assignment::factory()->create([
            'contact_id' => $contact->id,
            'template_id' => $template->id,
            'frequency_type' => 'daily_once',
            'is_advanced' => true,
            'send_times' => ['09:00'],
            'recurrence_rule' => [
                'unit' => 'weekly',
                'interval' => 1,
                'days_of_week' => [1],
                'day_of_month' => null,
                'month_of_year' => null,
            ],
            'start_date' => '2026-01-01',
            'timezone' => 'UTC',
            'channel' => 'sms',
        ]);

        $this->artisan('reminders:send')->assertSuccessful();

        Queue::assertPushed(SendReminderJob::class);

        Carbon::setTestNow();
    }

    public function test_scheduler_skips_advanced_weekly_rule_on_wrong_weekday(): void
    {
        Queue::fake();

        Carbon::setTestNow(Carbon::parse('2026-01-06 09:00:00', 'UTC'));

        $contact  = Contact::factory()->create();
        $template = Template::factory()->create();

        Assignment::factory()->create([
            'contact_id' => $contact->id,
            'template_id' => $template->id,
            'frequency_type' => 'daily_once',
            'is_advanced' => true,
            'send_times' => ['09:00'],
            'recurrence_rule' => [
                'unit' => 'weekly',
                'interval' => 1,
                'days_of_week' => [1],
                'day_of_month' => null,
                'month_of_year' => null,
            ],
            'start_date' => '2026-01-01',
            'timezone' => 'UTC',
            'channel' => 'sms',
        ]);

        $this->artisan('reminders:send')->assertSuccessful();

        Queue::assertNothingPushed();

        Carbon::setTestNow();
    }
}
