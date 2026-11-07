<?php

namespace App\Filament\App\Resources\Catalog;

use App\Filament\App\Resources\Catalog\Pages\ManageCatalogItems;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\catalog\Models\CatalogCategory;
use App\Modules\catalog\Models\CatalogItem;
use App\Modules\catalog\Models\CatalogUnit;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CatalogItemResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = CatalogItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Catalog items';

    protected static ?int $navigationSort = 20;

    protected static function moduleKey(): string
    {
        return 'catalog';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sku')->required()->maxLength(64),
            TextInput::make('name')->required(),
            Textarea::make('description')->columnSpanFull(),
            Select::make('category_id')
                ->options(fn (): array => CatalogCategory::query()->pluck('name', 'id')->all())
                ->searchable(),
            Select::make('unit_id')
                ->options(fn (): array => CatalogUnit::query()->pluck('name', 'id')->all())
                ->searchable(),
            TextInput::make('base_price')->numeric()->required(),
            TextInput::make('currency_code')->default('USD')->maxLength(3),
            Toggle::make('is_service'),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('base_price')->money(fn (CatalogItem $record) => $record->currency_code),
                IconColumn::make('is_active')->boolean(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCatalogItems::route('/'),
        ];
    }
}
