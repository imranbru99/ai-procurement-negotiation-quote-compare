<?php

namespace App\Filament\App\Resources\Quotes\Pages;

use App\Filament\App\Resources\Quotes\QuoteResource;
use App\Models\Quote;
use App\Modules\quotes\Services\QuoteService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submit')
                ->visible(fn (Quote $record): bool => in_array($record->status, ['draft', 'revised'], true))
                ->action(function (Quote $record, QuoteService $service): void {
                    $service->submit($record);
                    Notification::make()->title('Quote submitted')->success()->send();
                }),
            ...parent::getHeaderActions(),
        ];
    }
}
