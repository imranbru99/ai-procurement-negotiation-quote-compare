<?php

namespace App\Filament\App\Resources\MultiCurrency\Pages;

use App\Filament\App\Resources\MultiCurrency\CurrencyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCurrencies extends ManageRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
