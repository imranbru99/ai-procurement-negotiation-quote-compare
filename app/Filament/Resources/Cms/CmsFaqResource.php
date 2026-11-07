<?php

namespace App\Filament\Resources\Cms;

use App\Filament\Resources\Cms\Pages\ManageCmsFaqs;
use App\Modules\cms\Models\CmsFaq;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CmsFaqResource extends Resource
{
    protected static ?string $model = CmsFaq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?string $navigationLabel = 'FAQs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('locale')->required()->maxLength(8),
            TextInput::make('category')->default('general'),
            TextInput::make('question')->required()->columnSpanFull(),
            Textarea::make('answer')->required()->columnSpanFull(),
            TextInput::make('sort')->numeric()->default(0),
            DateTimePicker::make('published_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('locale'),
            TextColumn::make('category'),
            TextColumn::make('question')->searchable()->limit(50),
            TextColumn::make('sort'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCmsFaqs::route('/'),
        ];
    }
}
