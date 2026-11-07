<?php

namespace App\Filament\App\Resources\PriceIntelligence\Pages;

use App\Filament\App\Resources\PriceIntelligence\MarketPriceResource;
use Filament\Resources\Pages\ListRecords;

class ListMarketPrices extends ListRecords
{
    protected static string $resource = MarketPriceResource::class;
}
