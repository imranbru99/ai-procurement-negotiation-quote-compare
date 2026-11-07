<?php

namespace App\Filament\App\Resources\Documents;

use App\Filament\App\Resources\Documents\Pages\ManageDocuments;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\documents\Enums\DocumentVisibility;
use App\Modules\documents\Models\Document;
use App\Modules\suppliers\Models\Supplier;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DocumentResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocument;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 40;

    protected static function moduleKey(): string
    {
        return 'documents';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            TextInput::make('type')->default('general'),
            TextInput::make('version')->numeric()->default(1),
            DateTimePicker::make('expires_at'),
            Select::make('visibility')
                ->options(collect(DocumentVisibility::cases())->mapWithKeys(fn (DocumentVisibility $v) => [$v->value => str($v->name)->headline()])->all())
                ->required(),
            MorphToSelect::make('documentable')
                ->types([
                    MorphToSelect\Type::make(Supplier::class)->titleAttribute('name'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable(),
            TextColumn::make('type'),
            TextColumn::make('documentable_type')->label('Attached to'),
            TextColumn::make('visibility'),
            TextColumn::make('expires_at')->dateTime(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDocuments::route('/'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_by'] = Auth::id();

        return $data;
    }
}
