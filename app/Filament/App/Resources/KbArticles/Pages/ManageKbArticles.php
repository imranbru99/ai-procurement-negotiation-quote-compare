<?php

namespace App\Filament\App\Resources\KbArticles\Pages;

use App\Filament\App\Resources\KbArticles\KbArticleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageKbArticles extends ManageRecords
{
    protected static string $resource = KbArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
