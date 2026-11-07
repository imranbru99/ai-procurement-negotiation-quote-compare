<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteExtraction extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'quote_id',
        'source_type',
        'raw_text',
        'extracted',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'extracted' => 'array',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
