<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'activated_at' => $this->faker->dateTimeBetween('-6 months', '-3 months'),
            'appointment_at' => $this->faker->optional()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
