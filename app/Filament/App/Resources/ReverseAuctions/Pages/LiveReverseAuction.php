<?php

namespace App\Filament\App\Resources\ReverseAuctions\Pages;

use App\Filament\App\Resources\ReverseAuctions\ReverseAuctionResource;
use App\Models\AuctionBid;
use App\Models\ReverseAuction;
use Filament\Resources\Pages\Page;

class LiveReverseAuction extends Page
{
    protected static string $resource = ReverseAuctionResource::class;

    protected string $view = 'filament.app.pages.live-reverse-auction';

    public ReverseAuction $record;

    public function mount(ReverseAuction $record): void
    {
        $this->record = $record;
    }

    /**
     * @return list<AuctionBid>
     */
    public function getBidsProperty()
    {
        return $this->record->bids()->with('supplier')->orderByDesc('placed_at')->limit(20)->get();
    }
}
