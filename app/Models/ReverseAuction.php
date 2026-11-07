<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReverseAuction extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'rfq_id',
        'title',
        'status',
        'starts_at',
        'ends_at',
        'auto_extend_minutes',
        'min_bid_decrement',
        'currency_code',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'min_bid_decrement' => 'decimal:4',
        ];
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class, 'auction_id');
    }

    public function winningBid(): ?AuctionBid
    {
        return $this->bids()->where('is_winning', true)->latest('placed_at')->first();
    }

    public function isLive(): bool
    {
        if ($this->status !== 'live') {
            return false;
        }

        $now = now();

        return $this->starts_at !== null
            && $this->ends_at !== null
            && $now->gte($this->starts_at)
            && $now->lte($this->ends_at);
    }
}
