<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NegotiationStrategy extends Model
{
    protected $fillable = [
        'thread_id',
        'target_price',
        'walk_away_price',
        'batna',
        'leverage_points',
        'concession_plan',
        'reasoning',
        'confidence',
    ];

    protected function casts(): array
    {
        return [
            'target_price' => 'decimal:4',
            'walk_away_price' => 'decimal:4',
            'batna' => 'decimal:4',
            'leverage_points' => 'array',
            'concession_plan' => 'array',
            'confidence' => 'decimal:2',
        ];
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(NegotiationThread::class, 'thread_id');
    }
}
