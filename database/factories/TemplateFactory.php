<?php

namespace Database\Factories;

use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

class TemplateFactory extends Factory
{
    protected $model = Template::class;

    public function definition(): array
    {
        return [
            'name'    => $this->faker->words(3, true),
            'content' => 'Hello {name}, ' . $this->faker->sentence(),
        ];
    }
}
