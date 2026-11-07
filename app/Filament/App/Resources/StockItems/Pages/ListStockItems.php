<?php

namespace App\Filament\App\Resources\StockItems\Pages;

use App\Filament\App\Resources\StockItems\StockItemsResource;
use Filament\Resources\Pages\ListRecords;

class ListStockItems extends ListRecords
{
    protected static string $resource = StockItemsResource::class;
}
