<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Greeting;

/** @extends Factory<Greeting> */
class GreetingFactory extends Factory
{
    protected $model = Greeting::class;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'name' => $this->faker->name(),
            'message' => $this->faker->paragraph(),
        ];
    }

    public function withYoutube(): self
    {
        return $this->state(fn()=>[
            'message' => 'Xem video này: https://youtu.be/dQw4w9WgXcQ',
        ]);
    }
}

