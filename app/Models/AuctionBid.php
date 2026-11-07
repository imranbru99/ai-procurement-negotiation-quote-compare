<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionBid extends Model
{
    protected $fillable = [
        'auction_id',
        'supplier_id',
        'amount',
        'placed_at',
        'is_winning',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:4',
            'placed_at' => 'datetime',
            'is_winning' => 'boolean',
        ];
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(ReverseAuction::class, 'auction_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
