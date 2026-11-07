<?php

namespace App\Filament\Resources\Cms;

use App\Filament\Resources\Cms\Pages\ManageCmsPosts;
use App\Modules\cms\Models\CmsCategory;
use App\Modules\cms\Models\CmsPost;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CmsPostResource extends Resource
{
    protected static ?string $model = CmsPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?string $navigationLabel = 'Posts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('locale')->required()->maxLength(8),
            TextInput::make('slug'),
            TextInput::make('title')->required(),
            Select::make('cms_category_id')
                ->options(fn (): array => CmsCategory::query()->pluck('name', 'id')->all())
                ->searchable(),
            Textarea::make('excerpt')->columnSpanFull(),
            Select::make('body_format')->options(['html' => 'HTML', 'markdown' => 'Markdown'])->default('html'),
            Textarea::make('body')->columnSpanFull()->rows(12),
            TextInput::make('author_name'),
            TextInput::make('meta_title'),
            Textarea::make('meta_description')->columnSpanFull(),
            TextInput::make('og_image'),
            DateTimePicker::make('published_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('locale'),
            TextColumn::make('published_at')->dateTime()->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCmsPosts::route('/'),
        ];
    }
}
