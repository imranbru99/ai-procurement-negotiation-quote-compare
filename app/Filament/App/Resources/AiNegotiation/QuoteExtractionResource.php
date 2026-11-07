<?php

namespace App\Filament\App\Resources\AiNegotiation;

use App\Facades\Module;
use App\Filament\App\Resources\AiNegotiation\Pages\ListQuoteExtractions;
use App\Filament\App\Resources\AiNegotiation\Pages\ReviewQuoteExtraction;
use App\Models\QuoteExtraction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuoteExtractionResource extends Resource
{
    protected static ?string $model = QuoteExtraction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'AI Negotiation';

    public static function shouldRegisterNavigation(): bool
    {
        return Module::enabled('ai_negotiation');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('ai_negotiation.view') ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('source_type'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuoteExtractions::route('/'),
            'review' => ReviewQuoteExtraction::route('/{record}/review'),
        ];
    }
}
