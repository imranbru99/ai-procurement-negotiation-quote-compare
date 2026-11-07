<?php

namespace App\Filament\App\Resources\MultiCurrency;

use App\Filament\App\Resources\MultiCurrency\Pages\ManageExchangeRates;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\multi_currency\Models\Currency;
use App\Modules\multi_currency\Models\ExchangeRate;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExchangeRateResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = ExchangeRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Exchange rates';

    protected static ?int $navigationSort = 31;

    protected static ?string $slug = 'exchange-rates';

    protected static function moduleKey(): string
    {
        return 'multi_currency';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        $codes = fn (): array => Currency::query()->pluck('code', 'code')->all();

        return $schema->components([
            Select::make('base_code')->options($codes)->required(),
            Select::make('quote_code')->options($codes)->required(),
            TextInput::make('rate')->numeric()->required(),
            DatePicker::make('as_of')->required(),
            TextInput::make('source'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('base_code'),
            TextColumn::make('quote_code'),
            TextColumn::make('rate'),
            TextColumn::make('as_of')->date(),
            TextColumn::make('source'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageExchangeRates::route('/'),
        ];
    }
}
