<?php

namespace App\Filament\App\Resources\CostCenters;

use App\Filament\App\Resources\CostCenters\Pages\ListCostCenters;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\CostCenter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CostCentersResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = CostCenter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static function moduleKey(): string
    {
        return 'budgets';
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
            'index' => ListCostCenters::route('/'),
        ];
    }
}
