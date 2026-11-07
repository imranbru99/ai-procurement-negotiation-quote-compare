<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AiJob extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'feature',
        'status',
        'input',
        'output',
        'error',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'input' => 'array',
            'output' => 'array',
            'confirmed_at' => 'datetime',
        ];
    }
}
