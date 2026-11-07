<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NegotiationThread extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'rfq_id',
        'supplier_id',
        'status',
    ];

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(NegotiationRound::class, 'thread_id');
    }

    public function strategy(): HasOne
    {
        return $this->hasOne(NegotiationStrategy::class, 'thread_id');
    }
}
