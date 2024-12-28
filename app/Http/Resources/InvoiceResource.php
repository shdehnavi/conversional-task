<?php

namespace App\Http\Resources;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Invoice $this */
        return [
            'id' => $this->id,
            'customer' => new CustomerResource($this->customer),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_price' => new CurrencyResource($this->total_price),
            'registration_events_count' => $this->whenCounted('registrationEvents', fn () => $this->registration_events_count),
            'activation_events_count' => $this->whenCounted('activationEvents', fn () => $this->activation_events_count),
            'appointment_events_count' => $this->whenCounted('appointmentEvents', fn () => $this->appointment_events_count),
            'invoiced_events' => $this->whenLoaded('items', fn () => InvoiceItemResource::collection($this->items)),
        ];
    }
}
