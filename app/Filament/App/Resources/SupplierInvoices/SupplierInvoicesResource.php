<?php

namespace App\Filament\App\Resources\SupplierInvoices;

use App\Filament\App\Resources\SupplierInvoices\Pages\ListSupplierInvoices;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\SupplierInvoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierInvoicesResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = SupplierInvoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Procure-to-pay';

    protected static function moduleKey(): string
    {
        return 'invoices';
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
            'index' => ListSupplierInvoices::route('/'),
        ];
    }
}
