<?php

namespace App\Filament\App\Resources\SavingsRecords\Pages;

use App\Filament\App\Resources\SavingsRecords\SavingsRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSavingsRecords extends ManageRecords
{
    protected static string $resource = SavingsRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
