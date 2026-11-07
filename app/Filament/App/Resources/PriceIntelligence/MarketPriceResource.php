<?php

namespace App\Filament\App\Resources\PriceIntelligence;

use App\Filament\App\Resources\PriceIntelligence\Pages\ListMarketPrices;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\MarketPricePoint;
use App\Modules\price_intelligence\Jobs\ScrapeMarketPricesJob;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MarketPriceResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = MarketPricePoint::class;

    protected static ?string $navigationLabel = 'Market prices';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Intelligence';

    protected static ?int $navigationSort = 10;

    protected static function moduleKey(): string
    {
        return 'price_intelligence';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->searchable(),
                TextColumn::make('catalogItem.name')->label('Catalog item'),
                TextColumn::make('source_name'),
                TextColumn::make('price')->money(fn (MarketPricePoint $record) => $record->currency_code),
                TextColumn::make('scraped_at')->dateTime()->sortable(),
            ])
            ->defaultSort('scraped_at', 'desc')
            ->headerActions([
                Action::make('scrape')
                    ->label('Scrape catalog')
                    ->action(function (): void {
                        ScrapeMarketPricesJob::dispatchSync();
                        Notification::make()->title('Market scrape completed')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMarketPrices::route('/'),
        ];
    }
}
