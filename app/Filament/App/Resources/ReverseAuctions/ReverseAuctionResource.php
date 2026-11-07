<?php

namespace App\Filament\App\Resources\ReverseAuctions;

use App\Filament\App\Resources\ReverseAuctions\Pages\ListReverseAuctions;
use App\Filament\App\Resources\ReverseAuctions\Pages\LiveReverseAuction;
use App\Filament\Concerns\RegistersModuleNavigation;
use App\Models\ReverseAuction;
use App\Modules\reverse_auctions\Services\AuctionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReverseAuctionResource extends Resource
{
    use RegistersModuleNavigation;

    protected static ?string $model = ReverseAuction::class;

    protected static ?string $navigationLabel = 'Reverse auctions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingDown;

    protected static string|\UnitEnum|null $navigationGroup = 'Intelligence';

    protected static ?int $navigationSort = 50;

    protected static function moduleKey(): string
    {
        return 'reverse_auctions';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('starts_at')->dateTime(),
                TextColumn::make('ends_at')->dateTime(),
                TextColumn::make('currency_code'),
            ])
            ->recordActions([
                Action::make('live')
                    ->label('Live room')
                    ->url(fn (ReverseAuction $record): string => static::getUrl('live', ['record' => $record])),
                Action::make('goLive')
                    ->label('Go live')
                    ->visible(fn (ReverseAuction $record) => $record->status === 'draft')
                    ->action(function (ReverseAuction $record, AuctionService $auctions): void {
                        $auctions->goLive($record);
                        Notification::make()->title('Auction is live')->success()->send();
                    }),
            ])
            ->headerActions([
                Action::make('createAuction')
                    ->label('New auction')
                    ->form([
                        TextInput::make('title')->required(),
                        TextInput::make('min_bid_decrement')->numeric()->default(10),
                        TextInput::make('auto_extend_minutes')->numeric()->default(5),
                    ])
                    ->action(function (array $data): void {
                        ReverseAuction::query()->create([
                            'title' => $data['title'],
                            'status' => 'draft',
                            'min_bid_decrement' => $data['min_bid_decrement'] ?? 10,
                            'auto_extend_minutes' => $data['auto_extend_minutes'] ?? 5,
                            'currency_code' => 'USD',
                        ]);
                        Notification::make()->title('Auction created')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReverseAuctions::route('/'),
            'live' => LiveReverseAuction::route('/{record}/live'),
        ];
    }
}
