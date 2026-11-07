<?php

namespace App\Filament\App\Resources\Contracts;

use App\Filament\App\Resources\Contracts\Pages\ListContracts;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\Contract;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContractsResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = Contract::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Procure-to-pay';

    protected static function moduleKey(): string
    {
        return 'contracts';
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
            'index' => ListContracts::route('/'),
        ];
    }
}
