<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ScoringCriterion extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'weight',
        'higher_is_better',
        'field_key',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:4',
            'higher_is_better' => 'boolean',
        ];
    }
}
