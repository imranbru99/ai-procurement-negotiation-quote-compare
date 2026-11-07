<?php

namespace App\Filament\Supplier\Resources\PurchaseOrders;

use App\Filament\Supplier\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Models\PurchaseOrder;
use App\Modules\purchase_orders\Services\POService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable(),
                TextColumn::make('status'),
                TextColumn::make('total'),
            ])
            ->recordActions([
                Action::make('acknowledge')
                    ->visible(fn (PurchaseOrder $record): bool => $record->status === 'sent')
                    ->action(function (PurchaseOrder $record): void {
                        app(POService::class)->acknowledge($record);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseOrders::route('/'),
        ];
    }
}
