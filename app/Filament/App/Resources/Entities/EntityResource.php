<?php

namespace App\Filament\App\Resources\Entities;

use App\Filament\App\Resources\Entities\Pages\ManageEntities;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\Budget;
use App\Modules\multi_branch\Models\Entity;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EntityResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = Entity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'Organization';

    protected static ?string $navigationLabel = 'Branches / entities';

    protected static ?int $navigationSort = 10;

    protected static function moduleKey(): string
    {
        return 'multi_branch';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('code')->required()->maxLength(64),
            Select::make('budget_id')
                ->label('Budget')
                ->options(fn (): array => Budget::query()->pluck('year', 'id')->mapWithKeys(
                    fn ($year, $id) => [$id => 'Budget #'.$id.' ('.$year.')']
                )->all())
                ->searchable(),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('code')->searchable()->sortable(),
            TextColumn::make('budget_id')->label('Budget'),
            IconColumn::make('is_active')->boolean(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEntities::route('/'),
        ];
    }
}
