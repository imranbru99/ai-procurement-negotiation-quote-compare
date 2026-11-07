<?php

namespace App\Filament\App\Resources\SavingsRecords;

use App\Filament\App\Resources\SavingsRecords\Pages\ManageSavingsRecords;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\SavingsRecord;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SavingsRecordResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = SavingsRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Intelligence';

    protected static ?string $navigationLabel = 'Savings';

    protected static ?int $navigationSort = 50;

    protected static function moduleKey(): string
    {
        return 'savings';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('baseline_amount')->numeric()->required(),
            TextInput::make('negotiated_amount')->numeric()->required(),
            TextInput::make('hard_savings')->numeric()->required(),
            TextInput::make('soft_savings')->numeric()->default(0),
            TextInput::make('currency_code')->default('USD')->maxLength(3)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('baseline_amount')->numeric(decimalPlaces: 2),
            TextColumn::make('negotiated_amount')->numeric(decimalPlaces: 2),
            TextColumn::make('hard_savings')->numeric(decimalPlaces: 2),
            TextColumn::make('soft_savings')->numeric(decimalPlaces: 2),
            TextColumn::make('currency_code'),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSavingsRecords::route('/'),
        ];
    }
}
