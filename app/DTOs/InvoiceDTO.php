<?php

namespace App\DTOs;

use Illuminate\Support\Carbon;

readonly class InvoiceDTO
{
    public function __construct(
        public int $customerId,
        public Carbon $startDate,
        public Carbon $endDate,
    ) {}
}
