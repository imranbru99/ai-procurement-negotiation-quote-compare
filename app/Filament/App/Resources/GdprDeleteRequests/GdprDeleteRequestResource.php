<?php

namespace App\Filament\App\Resources\GdprDeleteRequests;

use App\Filament\App\Resources\GdprDeleteRequests\Pages\ManageGdprDeleteRequests;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\backup_gdpr\Models\GdprDeleteRequest;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GdprDeleteRequestResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = GdprDeleteRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Platform';

    protected static ?string $navigationLabel = 'GDPR requests';

    protected static ?int $navigationSort = 70;

    protected static function moduleKey(): string
    {
        return 'backup_gdpr';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'rejected' => 'Rejected',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('user.email')->label('User')->searchable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('requested_at')->dateTime()->sortable(),
            TextColumn::make('processed_at')->dateTime()->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGdprDeleteRequests::route('/'),
        ];
    }
}
