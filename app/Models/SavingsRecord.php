<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsRecord extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'rfq_id',
        'quote_id',
        'negotiation_thread_id',
        'buyer_id',
        'baseline_amount',
        'negotiated_amount',
        'hard_savings',
        'soft_savings',
        'currency_code',
    ];

    protected function casts(): array
    {
        return [
            'baseline_amount' => 'decimal:4',
            'negotiated_amount' => 'decimal:4',
            'hard_savings' => 'decimal:4',
            'soft_savings' => 'decimal:4',
        ];
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function negotiationThread(): BelongsTo
    {
        return $this->belongsTo(NegotiationThread::class);
    }
}
