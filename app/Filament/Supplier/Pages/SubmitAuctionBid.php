<?php

namespace App\Filament\Supplier\Pages;

use App\Facades\Module;
use App\Models\AuctionBid;
use App\Models\ReverseAuction;
use App\Models\Supplier;
use App\Modules\reverse_auctions\Services\AuctionService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class SubmitAuctionBid extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Reverse auctions';

    protected static ?int $navigationSort = 25;

    protected string $view = 'filament.supplier.pages.submit-auction-bid';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        $supplierId = Auth::user()?->supplier_id;
        if (! $supplierId) {
            return false;
        }

        $tenantId = Supplier::query()->whereKey($supplierId)->value('tenant_id');

        return Module::enabled('reverse_auctions', $tenantId);
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('auction_id')
                    ->label('Auction')
                    ->options(fn (): array => ReverseAuction::query()
                        ->where('status', 'live')
                        ->pluck('title', 'id')
                        ->all())
                    ->required()
                    ->live(),
                TextInput::make('amount')
                    ->numeric()
                    ->required(),
            ])
            ->statePath('data');
    }

    #[Computed]
    public function recentBids()
    {
        $auctionId = $this->data['auction_id'] ?? null;
        if (! $auctionId) {
            return collect();
        }

        return AuctionBid::query()
            ->where('auction_id', $auctionId)
            ->orderByDesc('placed_at')
            ->limit(10)
            ->get();
    }

    public function placeBid(AuctionService $auctions): void
    {
        $state = $this->form->getState();
        $supplierId = Auth::user()?->supplier_id;
        abort_unless($supplierId, 403);

        $auction = ReverseAuction::query()->findOrFail($state['auction_id']);
        $auctions->placeBid($auction, (int) $supplierId, (float) $state['amount']);

        Notification::make()->title('Bid placed')->success()->send();
        $this->form->fill(['auction_id' => $auction->id, 'amount' => null]);
    }
}
