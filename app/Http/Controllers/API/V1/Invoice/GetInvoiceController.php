<?php

namespace App\Http\Controllers\API\V1\Invoice;

use App\Contracts\Services\InvoiceServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;

class GetInvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceServiceInterface $invoiceService,
    ) {}

    /**
     * @response InvoiceResource
     */
    public function __invoke(Invoice $invoice)
    {
        $invoice = $this->invoiceService->getInvoice($invoice->id);

        return response()->apiSuccess(
            data: new InvoiceResource($invoice),
        );
    }
}
