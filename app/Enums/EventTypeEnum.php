<?php

namespace App\Enums;

enum EventTypeEnum: string
{
    case REGISTRATION = 'registration';
    case ACTIVATION = 'activation';
    case APPOINTMENT = 'appointment';

    public function price(): float
    {
        return match ($this) {
            self::REGISTRATION => 0.49,
            self::ACTIVATION => 0.99,
            self::APPOINTMENT => 3.99,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::REGISTRATION => 'Registration',
            self::ACTIVATION => 'Activation',
            self::APPOINTMENT => 'Appointment',
        };
    }
}
