<?php

namespace App\Http\Requests\API\V1\Invoice;

use App\DTOs\InvoiceDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|integer|exists:customers,id',
            'start' => 'required|date|date_format:Y-m-d',
            'end' => 'required|date|date_format:Y-m-d|after_or_equal::start_date',
        ];
    }

    public function toDTO(): InvoiceDTO
    {
        return new InvoiceDTO(
            customerId: $this->input('customer_id'),
            startDate: Carbon::parse($this->input('start')),
            endDate: Carbon::parse($this->input('end')),
        );
    }
}
