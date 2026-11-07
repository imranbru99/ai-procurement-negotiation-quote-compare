<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketPricePoint extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'catalog_item_id',
        'sku',
        'source_url',
        'source_name',
        'price',
        'currency_code',
        'scraped_at',
        'raw',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:4',
            'scraped_at' => 'datetime',
            'raw' => 'array',
        ];
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }
}
