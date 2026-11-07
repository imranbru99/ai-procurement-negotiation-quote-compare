<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'user_id',
        'feature',
        'provider',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'cost',
        'status',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:6',
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
