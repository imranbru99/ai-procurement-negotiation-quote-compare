<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteComparison extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'rfq_id',
        'name',
        'weights',
        'results',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'weights' => 'array',
            'results' => 'array',
        ];
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
