<?php

namespace App\Filament\Supplier\Resources\RfqInvitations;

use App\Filament\Supplier\Resources\RfqInvitations\Pages\ListRfqInvitations;
use App\Models\RfqInvitation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RfqInvitationResource extends Resource
{
    protected static ?string $model = RfqInvitation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?string $navigationLabel = 'RFQ invitations';

    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        $supplierId = Auth::user()?->supplier_id;

        return parent::getEloquentQuery()
            ->when($supplierId, fn (Builder $query) => $query->where('supplier_id', $supplierId));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rfq.number')->label('RFQ'),
                TextColumn::make('rfq.title'),
                TextColumn::make('status')->badge(),
                TextColumn::make('invited_at')->dateTime(),
            ])
            ->defaultSort('invited_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRfqInvitations::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
