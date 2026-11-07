<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceAlert extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'catalog_item_id',
        'threshold_percent',
        'direction',
        'last_triggered_at',
    ];

    protected function casts(): array
    {
        return [
            'threshold_percent' => 'decimal:2',
            'last_triggered_at' => 'datetime',
        ];
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }
}
