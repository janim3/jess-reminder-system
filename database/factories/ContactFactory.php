<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'name'          => $this->faker->name(),
            'phone_number'  => $this->faker->unique()->e164PhoneNumber(),
            'email'         => $this->faker->optional()->safeEmail(),
            'date_of_birth' => $this->faker->optional()->date(),
        ];
    }
}
