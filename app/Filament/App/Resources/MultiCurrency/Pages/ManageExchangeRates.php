<?php

namespace App\Filament\App\Resources\MultiCurrency\Pages;

use App\Filament\App\Resources\MultiCurrency\ExchangeRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageExchangeRates extends ManageRecords
{
    protected static string $resource = ExchangeRateResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
