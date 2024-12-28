<?php

namespace Database\Factories;

use App\Enums\EventTypeEnum;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'user_id' => User::factory(),
            'event_type' => $this->faker->randomElement(EventTypeEnum::cases()),
            'event_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'price' => $this->faker->randomFloat(2, EventTypeEnum::REGISTRATION->price(), EventTypeEnum::APPOINTMENT->price()),
            'parent_invoice_item_id' => null,
        ];
    }
}
