<?php

namespace App\Modules\reverse_auctions\Services;

use App\Models\AuctionBid;
use App\Models\ReverseAuction;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AuctionService
{
    public function placeBid(ReverseAuction $auction, int $supplierId, float $amount): AuctionBid
    {
        $auction->refresh();

        if (! $auction->isLive()) {
            throw ValidationException::withMessages([
                'amount' => 'Auction is not accepting bids.',
            ]);
        }

        $currentWinning = $auction->winningBid();
        if ($currentWinning !== null) {
            $minimum = (float) $currentWinning->amount - (float) $auction->min_bid_decrement;
            if ($amount > $minimum) {
                throw ValidationException::withMessages([
                    'amount' => "Bid must be at most {$minimum} {$auction->currency_code}.",
                ]);
            }
        } elseif ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Opening bid must be positive.',
            ]);
        }

        $placedAt = now();
        $endsAt = $auction->ends_at;

        if ($endsAt instanceof Carbon && $placedAt->diffInMinutes($endsAt, false) <= $auction->auto_extend_minutes) {
            $auction->forceFill([
                'ends_at' => $endsAt->copy()->addMinutes($auction->auto_extend_minutes),
            ])->save();
        }

        AuctionBid::query()
            ->where('auction_id', $auction->id)
            ->where('is_winning', true)
            ->update(['is_winning' => false]);

        return AuctionBid::query()->create([
            'auction_id' => $auction->id,
            'supplier_id' => $supplierId,
            'amount' => $amount,
            'placed_at' => $placedAt,
            'is_winning' => true,
        ]);
    }

    public function goLive(ReverseAuction $auction, ?Carbon $startsAt = null, ?Carbon $endsAt = null): ReverseAuction
    {
        $auction->forceFill([
            'status' => 'live',
            'starts_at' => $startsAt ?? now()->subMinute(),
            'ends_at' => $endsAt ?? now()->addHour(),
        ])->save();

        return $auction->refresh();
    }
}
