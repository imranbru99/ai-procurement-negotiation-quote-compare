<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NegotiationRound extends Model
{
    protected $fillable = [
        'thread_id',
        'round_number',
        'direction',
        'offer_payload',
        'message_body',
        'channel',
        'created_by',
        'ai_generated',
        'delta_amount',
        'savings_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'offer_payload' => 'array',
            'ai_generated' => 'boolean',
            'delta_amount' => 'decimal:4',
            'savings_amount' => 'decimal:4',
        ];
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(NegotiationThread::class, 'thread_id');
    }
}
