<?php

use App\Modules\integrations\Http\Controllers\Api\V1\PurchaseOrderController;
use App\Modules\integrations\Http\Controllers\Api\V1\QuoteController;
use App\Modules\integrations\Http\Controllers\Api\V1\RfqController;
use App\Modules\integrations\Http\Controllers\Api\V1\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['auth:sanctum', 'tenant.context', 'module:integrations'])
    ->group(function (): void {
        Route::get('suppliers', [SupplierController::class, 'index'])->middleware('module:suppliers');
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->middleware('module:suppliers');

        Route::get('rfqs', [RfqController::class, 'index'])->middleware('module:rfq');
        Route::get('rfqs/{rfq}', [RfqController::class, 'show'])->middleware('module:rfq');

        Route::get('quotes', [QuoteController::class, 'index'])->middleware('module:quotes');
        Route::get('quotes/{quote}', [QuoteController::class, 'show'])->middleware('module:quotes');

        Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])->middleware('module:purchase_orders');
        Route::get('purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'show'])->middleware('module:purchase_orders');
    });
