<?php

namespace App\Filament\App\Resources\Tasks;

use App\Filament\App\Resources\Tasks\Pages\ManageTasks;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\tasks\Models\Task;
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

class TaskResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = Task::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Collaboration';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?int $navigationSort = 20;

    protected static function moduleKey(): string
    {
        return 'tasks';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            Textarea::make('description')->columnSpanFull(),
            DateTimePicker::make('due_at'),
            Select::make('status')
                ->options([
                    'open' => 'Open',
                    'in_progress' => 'In progress',
                    'done' => 'Done',
                    'cancelled' => 'Cancelled',
                ])
                ->default('open')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('title')->searchable()->limit(50),
            TextColumn::make('status')->badge(),
            TextColumn::make('due_at')->dateTime()->sortable(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTasks::route('/'),
        ];
    }
}
