<?php

namespace App\Filament\App\Resources\SupportTickets;

use App\Filament\App\Resources\SupportTickets\Pages\ManageSupportTickets;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Modules\support\Models\SupportTicket;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SupportTicketResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = SupportTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static string|\UnitEnum|null $navigationGroup = 'Platform';

    protected static ?string $navigationLabel = 'Support tickets';

    protected static ?int $navigationSort = 55;

    protected static function moduleKey(): string
    {
        return 'support';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('subject')->required()->maxLength(255),
            Textarea::make('body')->required()->columnSpanFull(),
            Select::make('status')
                ->options([
                    'open' => 'Open',
                    'pending' => 'Pending',
                    'resolved' => 'Resolved',
                    'closed' => 'Closed',
                ])
                ->default('open')
                ->required(),
            Select::make('priority')
                ->options([
                    'low' => 'Low',
                    'normal' => 'Normal',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                ])
                ->default('normal')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('subject')->searchable()->limit(40),
            TextColumn::make('status')->badge(),
            TextColumn::make('priority')->badge(),
            TextColumn::make('user.name')->label('Requester'),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSupportTickets::route('/'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] ??= Auth::id();

        return $data;
    }
}
