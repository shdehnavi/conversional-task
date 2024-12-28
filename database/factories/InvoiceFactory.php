<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'start_date' => $this->faker->dateTimeBetween('-1 year', '-6 months'),
            'end_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'total_price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
