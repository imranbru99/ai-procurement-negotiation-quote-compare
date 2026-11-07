<?php

namespace App\Filament\App\Resources\Comments;

use App\Filament\App\Resources\Comments\Pages\ManageComments;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\collaboration\Models\Comment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommentResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = Comment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|\UnitEnum|null $navigationGroup = 'Collaboration';

    protected static ?string $navigationLabel = 'Comments';

    protected static ?int $navigationSort = 30;

    protected static function moduleKey(): string
    {
        return 'collaboration';
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('body')->limit(60)->searchable(),
            TextColumn::make('user.name')->label('Author'),
            TextColumn::make('commentable_type')->label('On'),
            IconColumn::make('internal_only')->boolean(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageComments::route('/'),
        ];
    }
}
