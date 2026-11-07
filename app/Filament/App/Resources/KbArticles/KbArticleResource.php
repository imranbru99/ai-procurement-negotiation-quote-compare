<?php

namespace App\Filament\App\Resources\KbArticles;

use App\Filament\App\Resources\KbArticles\Pages\ManageKbArticles;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\knowledge_base\Models\KbArticle;
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

class KbArticleResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = KbArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Knowledge base';

    protected static ?int $navigationSort = 20;

    protected static function moduleKey(): string
    {
        return 'knowledge_base';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('locale')->required()->maxLength(8)->default('en'),
            TextInput::make('slug')->required()->maxLength(255),
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('category')->maxLength(120),
            Select::make('body_format')
                ->options(['html' => 'HTML', 'markdown' => 'Markdown'])
                ->default('markdown')
                ->required(),
            Textarea::make('body')->columnSpanFull()->rows(12),
            TextInput::make('meta_title'),
            Textarea::make('meta_description')->columnSpanFull(),
            DateTimePicker::make('published_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('locale')->sortable(),
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('category')->sortable(),
            TextColumn::make('slug')->searchable(),
            TextColumn::make('published_at')->dateTime()->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageKbArticles::route('/'),
        ];
    }
}
