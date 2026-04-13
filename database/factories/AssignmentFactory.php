<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Contact;
use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        $frequencyType = $this->faker->randomElement(['daily_once', 'daily_twice', 'daily_thrice', 'weekly']);

        $timeCounts = ['daily_once' => 1, 'daily_twice' => 2, 'daily_thrice' => 3, 'weekly' => 1];
        $count      = $timeCounts[$frequencyType];

        $sendTimes = [];
        for ($i = 0; $i < $count; $i++) {
            $sendTimes[] = sprintf('%02d:00', $this->faker->numberBetween(6, 21));
        }

        return [
            'contact_id'     => Contact::factory(),
            'template_id'    => Template::factory(),
            'frequency_type' => $frequencyType,
            'send_times'     => $sendTimes,
            'channel'        => $this->faker->randomElement(['sms', 'email', 'both']),
        ];
    }
}
