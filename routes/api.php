<?php

use App\Http\Controllers\API\V1\Invoice\CreateInvoiceController;
use App\Http\Controllers\API\V1\Invoice\GetInvoiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('invoices')
    ->middleware([
        'throttle:api',
    ])
    ->group(function () {
        Route::post('/', CreateInvoiceController::class);
        Route::get('/{invoice}', GetInvoiceController::class);
    });
