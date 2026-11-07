<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqInvitation extends Model
{
    protected $fillable = [
        'rfq_id',
        'supplier_id',
        'token',
        'status',
        'invited_at',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'invited_at' => 'datetime',
            'viewed_at' => 'datetime',
        ];
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
