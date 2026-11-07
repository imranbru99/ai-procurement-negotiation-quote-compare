<?php

namespace App\Filament\App\Resources\AiNegotiation\Pages;

use App\Filament\App\Resources\AiNegotiation\QuoteExtractionResource;
use App\Models\QuoteExtraction;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ReviewQuoteExtraction extends Page
{
    protected static string $resource = QuoteExtractionResource::class;

    protected string $view = 'filament.app.pages.review-quote-extraction';

    public QuoteExtraction $record;

    public function mount(QuoteExtraction $record): void
    {
        $this->record = $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn () => $this->record->update(['status' => 'approved'])),
            Action::make('reject')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->record->update(['status' => 'rejected'])),
        ];
    }
}
