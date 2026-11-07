<?php

namespace App\Filament\App\Resources\StockItems;

use App\Filament\App\Resources\StockItems\Pages\ListStockItems;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\StockItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockItemsResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = StockItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Procure-to-pay';

    protected static function moduleKey(): string
    {
        return 'inventory';
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockItems::route('/'),
        ];
    }
}
