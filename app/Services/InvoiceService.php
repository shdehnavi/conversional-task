<?php

namespace App\Services;

use App\Contracts\Services\InvoiceServiceInterface;
use App\DTOs\EventDTO;
use App\DTOs\InvoiceDTO;
use App\Enums\EventTypeEnum;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Session;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InvoiceService implements InvoiceServiceInterface
{
    public function createInvoice(InvoiceDTO $invoiceDTO): Invoice
    {
        try {
            DB::beginTransaction();

            $invoice = new Invoice;
            $invoice->customer_id = $invoiceDTO->customerId;
            $invoice->start_date = $invoiceDTO->startDate;
            $invoice->end_date = $invoiceDTO->endDate;
            $invoice->total_price = 0;
            $invoice->save();

            // Retrieve all users for the given customer
            $users = User::query()
                ->where('customer_id', $invoiceDTO->customerId)
                ->get();

            $totalAmount = 0;

            $users->each(function (User $user) use (&$totalAmount, $invoice, $invoiceDTO) {
                $events = $this->gatherEventsForPeriod($user, $invoiceDTO->startDate, $invoiceDTO->endDate);

                $highestEventDTO = $this->findHighestEvent($events);
                if (! $highestEventDTO) {
                    return; // No events in period
                }

                // Compare with previously invoiced event
                $previousInvoiceItem = $this->getPreviouslyInvoicedEvent($user);
                $priceDiff = $this->calculatePriceDifference($previousInvoiceItem, $highestEventDTO);

                if ($priceDiff <= 0) {
                    return; // No difference to invoice
                }

                $invoiceItem = new InvoiceItem;
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->user_id = $user->id;
                $invoiceItem->event_date = $highestEventDTO->date;
                $invoiceItem->event_type = $highestEventDTO->type;
                $invoiceItem->price = $priceDiff;
                $invoiceItem->parent_invoice_item_id = $previousInvoiceItem?->id;
                $invoiceItem->save();

                $totalAmount += $priceDiff;
            });

            $invoice->total_price = $totalAmount;
            $invoice->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return $invoice;
    }

    public function getInvoice(int $id): Invoice
    {
        return Invoice::query()
            ->where('id', $id)
            ->with([
                'items.user',
                'items.parentInvoiceItem',
            ])
            ->withCount([
                'registrationEvents' => function (Builder $query) {
                    $query->whereBetweenColumns('users.created_at', ['start_date', 'end_date']);
                },
                'activationEvents' => function (Builder $query) {
                    $query->whereBetweenColumns('sessions.activated_at', ['start_date', 'end_date']);
                },
                'appointmentEvents' => function (Builder $query) {
                    $query->whereBetweenColumns('sessions.appointment_at', ['start_date', 'end_date']);
                },
            ])
            ->firstOrFail();
    }

    private function gatherEventsForPeriod(User $user, Carbon $startDate, Carbon $endDate): Collection
    {
        $events = collect();

        // User registration
        if ($user->created_at->between($startDate, $endDate)) {
            $registrationEvent = new EventDTO(
                EventTypeEnum::REGISTRATION,
                $user->created_at,
                EventTypeEnum::REGISTRATION->price()
            );

            $events->push($registrationEvent);
        }

        // Activation sessions
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Session> $activationSessions */
        $activationSessions = $user->sessions()
            ->whereBetween('activated_at', [$startDate, $endDate])
            ->get();

        $activationSessions->each(function (Session $session) use ($events) {
            $activationEvent = new EventDTO(
                EventTypeEnum::ACTIVATION,
                $session->activated_at,
                EventTypeEnum::ACTIVATION->price()
            );

            $events->push($activationEvent);
        });

        // Appointment sessions
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Session> $appointmentSessions */
        $appointmentSessions = $user->sessions()
            ->whereBetween('appointment_at', [$startDate, $endDate])
            ->get();

        $appointmentSessions->each(function (Session $session) use ($events) {
            $appointmentEvent = new EventDTO(
                EventTypeEnum::APPOINTMENT,
                $session->appointment_at,
                EventTypeEnum::APPOINTMENT->price()
            );

            $events->push($appointmentEvent);
        });

        return $events;
    }

    private function findHighestEvent(Collection $events): ?EventDTO
    {
        if ($events->isEmpty()) {
            return null;
        }

        return $events
            ->sortByDesc(fn (EventDTO $event) => $event->price)
            ->first();
    }

    private function calculatePriceDifference(?InvoiceItem $parentItem, EventDTO $newEvent): float
    {
        $previousPrice = $parentItem?->event_type->price() ?? 0;

        return $newEvent->price - $previousPrice;
    }

    private function getPreviouslyInvoicedEvent(User $user): ?InvoiceItem
    {
        /** @var ?InvoiceItem $invoiceItem */
        $invoiceItem = $user->invoiceItems()
            ->orderByDesc('price')
            ->first();

        return $invoiceItem;
    }
}
