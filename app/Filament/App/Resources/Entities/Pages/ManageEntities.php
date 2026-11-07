<?php

namespace App\Filament\App\Resources\Entities\Pages;

use App\Filament\App\Resources\Entities\EntityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEntities extends ManageRecords
{
    protected static string $resource = EntityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
