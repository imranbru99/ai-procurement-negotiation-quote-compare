<?php

namespace App\Filament\App\Resources\Warehouses;

use App\Filament\App\Resources\Warehouses\Pages\ListWarehouses;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\Warehouse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WarehousesResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = Warehouse::class;

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
            'index' => ListWarehouses::route('/'),
        ];
    }
}
