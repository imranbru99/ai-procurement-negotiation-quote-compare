<?php

namespace App\Filament\Resources\Cms\Pages;

use App\Filament\Resources\Cms\CmsFaqResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCmsFaqs extends ManageRecords
{
    protected static string $resource = CmsFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
