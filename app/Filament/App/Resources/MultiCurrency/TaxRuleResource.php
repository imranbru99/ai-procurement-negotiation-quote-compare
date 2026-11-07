<?php

namespace App\Filament\App\Resources\MultiCurrency;

use App\Filament\App\Resources\MultiCurrency\Pages\ManageTaxRules;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\multi_currency\Models\TaxRule;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaxRuleResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = TaxRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Tax rules';

    protected static ?int $navigationSort = 32;

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
        return $schema->components([
            TextInput::make('country_code')->required()->maxLength(2),
            TextInput::make('name')->required(),
            TextInput::make('rate_percent')->numeric()->required(),
            TextInput::make('type')->default('vat'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('country_code'),
            TextColumn::make('name'),
            TextColumn::make('rate_percent'),
            TextColumn::make('type'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTaxRules::route('/'),
        ];
    }
}
