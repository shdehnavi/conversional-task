<?php

use App\Contracts\Services\InvoiceServiceInterface;
use App\DTOs\InvoiceDTO;
use App\Enums\EventTypeEnum;
use App\Models\Customer;
use App\Models\Session;
use App\Models\User;

beforeEach(function () {
    $this->customer = Customer::factory()->create();

    $this->invoiceService = app(InvoiceServiceInterface::class);
});

test('creates invoice with highest event if user has multiple events in the period', function () {
    $user = User::factory()
        ->for($this->customer)
        ->create([
            'created_at' => now()->subDays(5),
        ]);

    Session::factory()
        ->for($user)
        ->create([
            'activated_at' => now()->subDays(2),
        ]);

    Session::factory()
        ->for($user)
        ->create([
            'appointment_at' => now()->subDay(),
        ]);

    $invoiceDTO = new InvoiceDTO(
        customerId: $this->customer->id,
        startDate: now()->subDays(7),
        endDate: now(),
    );

    $invoice = $this->invoiceService->createInvoice($invoiceDTO);

    // Expect only one InvoiceItem, for the highest event = appointment (3.99)
    expect($invoice->items)->toHaveCount(1)
        ->and($invoice->items[0]->event_type)->toBe(EventTypeEnum::APPOINTMENT)
        ->and($invoice->items[0]->price)->toBe(EventTypeEnum::APPOINTMENT->price())
        ->and($invoice->total_price)->toBe(EventTypeEnum::APPOINTMENT->price());
});

test('invoices only the difference if user was previously invoiced for a cheaper event', function () {
    $user = User::factory()
        ->for($this->customer)
        ->create([
            'created_at' => now()->subDays(45),
        ]);

    $firstInvoiceDTO = new InvoiceDTO(
        customerId: $this->customer->id,
        startDate: now()->subMonths(2),
        endDate: now()->subMonth(),
    );
    $firstInvoice = $this->invoiceService->createInvoice($firstInvoiceDTO);

    Session::factory()
        ->for($user)
        ->create([
            'activated_at' => now()->subDays(15),
        ]);

    $secondInvoiceDTO = new InvoiceDTO(
        customerId: $this->customer->id,
        startDate: now()->subMonth(),
        endDate: now(),
    );

    $secondInvoice = $this->invoiceService->createInvoice($secondInvoiceDTO);

    // The difference: activation(0.99) - registration(0.49) = 0.50
    expect($secondInvoice->items)->toHaveCount(1)
        ->and($secondInvoice->total_price)->toBe(0.50)
        ->and($secondInvoice->items[0]->event_type)->toBe(EventTypeEnum::ACTIVATION)
        ->and($secondInvoice->items[0]->price)->toBe(0.50)
        ->and($secondInvoice->items[0]->parentInvoiceItem?->event_type)->toBe(EventTypeEnum::REGISTRATION);
});
