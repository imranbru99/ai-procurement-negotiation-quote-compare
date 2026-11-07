<?php

namespace App\Filament\App\Resources\PurchaseOrders;

use App\Filament\App\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\PurchaseOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseOrdersResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = PurchaseOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Procure-to-pay';

    protected static function moduleKey(): string
    {
        return 'purchase_orders';
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
            'index' => ListPurchaseOrders::route('/'),
        ];
    }
}
