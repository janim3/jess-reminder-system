<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'contact_id',
        'template_id',
        'frequency_type',
        'is_advanced',
        'send_times',
        'recurrence_rule',
        'channel',
        'start_date',
        'end_date',
        'timezone',
    ];

    protected $casts = [
        'send_times' => 'array',
        'recurrence_rule' => 'array',
        'is_advanced' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function shouldSendAt(string $time): bool
    {
        return in_array($time, $this->send_times ?? []);
    }

    public function shouldSendNow(Carbon $now): bool
    {
        $timezoneNow = $this->timezone ? $now->copy()->setTimezone($this->timezone) : $now->copy();

        if (!$this->is_advanced) {
            $currentTime = $timezoneNow->format('H:i');
            return in_array($currentTime, $this->send_times ?? [], true);
        }

        $rule = $this->recurrence_rule ?? [];
        $unit = $rule['unit'] ?? null;

        if (in_array($unit, ['seconds', 'minutes', 'hours'], true)) {
            return $this->matchesAdvancedRule($timezoneNow);
        }

        $currentTime = $timezoneNow->format('H:i');
        if (!in_array($currentTime, $this->send_times ?? [], true)) {
            return false;
        }

        return $this->matchesAdvancedRule($timezoneNow);
    }

    private function matchesAdvancedRule(Carbon $now): bool
    {
        $rule = $this->recurrence_rule ?? [];
        $unit = $rule['unit'] ?? null;
        $interval = max(1, (int) ($rule['interval'] ?? 1));

        if (!$unit) {
            return false;
        }

        if ($this->start_date && $now->toDateString() < $this->start_date->toDateString()) {
            return false;
        }

        if ($this->end_date && $now->toDateString() > $this->end_date->toDateString()) {
            return false;
        }

        $anchor = $this->start_date
            ? Carbon::parse($this->start_date->toDateString(), $now->timezone)
            : $now->copy()->startOfDay();

        $firstTime = $this->send_times[0] ?? '00:00';
        if (preg_match('/^\d{2}:\d{2}$/', $firstTime) === 1) {
            [$hour, $minute] = array_map('intval', explode(':', $firstTime));
            $anchor->setTime($hour, $minute);
        }

        return match ($unit) {
            'seconds' => $this->matchesElapsedRule($anchor, $now, $interval, 'seconds'),
            'minutes' => $this->matchesElapsedRule($anchor, $now, $interval, 'minutes'),
            'hours' => $this->matchesElapsedRule($anchor, $now, $interval, 'hours'),
            'daily' => $anchor->diffInDays($now) % $interval === 0,
            'weekly' => $this->matchesWeeklyRule($rule, $anchor, $now, $interval),
            'monthly' => $this->matchesMonthlyRule($rule, $anchor, $now, $interval),
            'yearly' => $this->matchesYearlyRule($rule, $anchor, $now, $interval),
            default => false,
        };
    }

    private function matchesElapsedRule(Carbon $anchor, Carbon $now, int $interval, string $unit): bool
    {
        if ($now->lt($anchor)) {
            return false;
        }

        return match ($unit) {
            'seconds' => $anchor->diffInSeconds($now) % $interval === 0,
            'minutes' => $anchor->diffInMinutes($now) % $interval === 0,
            'hours' => $anchor->diffInHours($now) % $interval === 0,
            default => false,
        };
    }

    private function matchesWeeklyRule(array $rule, Carbon $anchor, Carbon $now, int $interval): bool
    {
        $days = array_map('intval', $rule['days_of_week'] ?? []);
        if (empty($days) || !in_array((int) $now->dayOfWeekIso, $days, true)) {
            return false;
        }

        return $anchor->startOfWeek()->diffInWeeks($now->copy()->startOfWeek()) % $interval === 0;
    }

    private function matchesMonthlyRule(array $rule, Carbon $anchor, Carbon $now, int $interval): bool
    {
        $dayOfMonth = (int) ($rule['day_of_month'] ?? 0);
        if ($dayOfMonth < 1 || $dayOfMonth > 31) {
            return false;
        }

        $lastDay = $now->copy()->endOfMonth()->day;
        if ($now->day !== min($dayOfMonth, $lastDay)) {
            return false;
        }

        return $anchor->copy()->startOfMonth()->diffInMonths($now->copy()->startOfMonth()) % $interval === 0;
    }

    private function matchesYearlyRule(array $rule, Carbon $anchor, Carbon $now, int $interval): bool
    {
        $monthOfYear = (int) ($rule['month_of_year'] ?? 0);
        $dayOfMonth = (int) ($rule['day_of_month'] ?? 0);

        if ($monthOfYear < 1 || $monthOfYear > 12 || $dayOfMonth < 1 || $dayOfMonth > 31) {
            return false;
        }

        if ($now->month !== $monthOfYear) {
            return false;
        }

        $lastDay = $now->copy()->endOfMonth()->day;
        if ($now->day !== min($dayOfMonth, $lastDay)) {
            return false;
        }

        return $anchor->copy()->startOfYear()->diffInYears($now->copy()->startOfYear()) % $interval === 0;
    }
}
