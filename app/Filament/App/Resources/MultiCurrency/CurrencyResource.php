<?php

namespace App\Filament\App\Resources\MultiCurrency;

use App\Filament\App\Resources\MultiCurrency\Pages\ManageCurrencies;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\multi_currency\Models\Currency;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = Currency::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 30;

    protected static function moduleKey(): string
    {
        return 'multi_currency';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')->required()->length(3)->uppercase(),
            TextInput::make('name')->required(),
            TextInput::make('symbol')->required()->maxLength(8),
            TextInput::make('decimal_places')->numeric()->default(2),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code'),
            TextColumn::make('name'),
            TextColumn::make('symbol'),
            IconColumn::make('is_active')->boolean(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCurrencies::route('/'),
        ];
    }
}
