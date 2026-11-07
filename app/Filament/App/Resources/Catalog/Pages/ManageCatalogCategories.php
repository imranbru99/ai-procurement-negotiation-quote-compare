<?php

namespace App\Filament\App\Resources\Catalog\Pages;

use App\Filament\App\Resources\Catalog\CatalogCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCatalogCategories extends ManageRecords
{
    protected static string $resource = CatalogCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
