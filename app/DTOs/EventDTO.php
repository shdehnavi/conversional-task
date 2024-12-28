<?php

namespace App\DTOs;

use App\Enums\EventTypeEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

class EventDTO implements Arrayable
{
    public function __construct(
        public EventTypeEnum $type,
        public Carbon $date,
        public float $price,
    ) {}

    public function toArray(): array
    {
        return [
            'event_type' => $this->type->value,
            'event_date' => $this->date->toDateString(),
            'price' => $this->price,
        ];
    }
}
