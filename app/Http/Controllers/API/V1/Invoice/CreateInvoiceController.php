<?php

namespace App\Http\Controllers\API\V1\Invoice;

use App\Contracts\Services\InvoiceServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Invoice\CreateInvoiceRequest;
use App\Http\Resources\CreateInvoiceResource;

class CreateInvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceServiceInterface $invoiceService,
    ) {}

    /**
     * @response SimpleInvoiceResource
     */
    public function __invoke(CreateInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->createInvoice($request->toDTO());

        return response()->apiSuccess(
            data: new CreateInvoiceResource($invoice),
            messages: 'Invoice has been created successfully.',
        );
    }
}
