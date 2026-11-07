<?php

namespace App\Filament\App\Resources\Catalog\Pages;

use App\Filament\App\Resources\Catalog\CatalogItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCatalogItems extends ManageRecords
{
    protected static string $resource = CatalogItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
