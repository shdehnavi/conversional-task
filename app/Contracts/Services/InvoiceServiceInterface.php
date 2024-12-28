<?php

namespace App\Contracts\Services;

use App\DTOs\InvoiceDTO;
use App\Models\Invoice;

interface InvoiceServiceInterface
{
    public function createInvoice(InvoiceDTO $invoiceDTO): Invoice;

    public function getInvoice(int $id): Invoice;
}
