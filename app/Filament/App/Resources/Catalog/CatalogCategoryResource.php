<?php

namespace App\Filament\App\Resources\Catalog;

use App\Filament\App\Resources\Catalog\Pages\ManageCatalogCategories;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\catalog\Models\CatalogCategory;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CatalogCategoryResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = CatalogCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Catalog categories';

    protected static ?int $navigationSort = 21;

    protected static function moduleKey(): string
    {
        return 'catalog';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            Select::make('parent_id')
                ->label('Parent')
                ->options(fn (): array => CatalogCategory::query()->pluck('name', 'id')->all())
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('path'),
            TextColumn::make('parent.name')->label('Parent'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCatalogCategories::route('/'),
        ];
    }
}
