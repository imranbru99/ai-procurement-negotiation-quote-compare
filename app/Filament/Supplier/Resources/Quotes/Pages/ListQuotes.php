<?php

namespace App\Filament\Supplier\Resources\Quotes\Pages;

use App\Filament\Supplier\Resources\Quotes\QuoteResource;
use Filament\Resources\Pages\ListRecords;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;
}
