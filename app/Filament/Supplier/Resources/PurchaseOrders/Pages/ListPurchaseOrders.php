<?php

namespace App\Filament\Supplier\Resources\PurchaseOrders\Pages;

use App\Filament\Supplier\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseOrders extends ListRecords
{
    protected static string $resource = PurchaseOrderResource::class;
}
