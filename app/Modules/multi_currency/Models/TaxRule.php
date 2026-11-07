<?php

namespace App\Modules\multi_currency\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'country_code',
        'name',
        'rate_percent',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'rate_percent' => 'decimal:4',
        ];
    }
}
