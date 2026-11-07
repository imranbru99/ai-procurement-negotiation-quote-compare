<?php

namespace App\Models;

use App\Modules\catalog\Models\CatalogUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionLine extends Model
{
    protected $fillable = [
        'requisition_id',
        'catalog_item_id',
        'description',
        'qty',
        'unit_id',
        'estimated_unit_price',
        'currency_code',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:4',
            'estimated_unit_price' => 'decimal:4',
        ];
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'requisition_id');
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(CatalogUnit::class, 'unit_id');
    }
}
