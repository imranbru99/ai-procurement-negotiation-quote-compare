<?php

namespace App\Models;

use App\Modules\catalog\Models\CatalogUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RfqLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'catalog_item_id',
        'description',
        'qty',
        'unit_id',
        'target_price',
        'uom',
        'line_number',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:4',
            'target_price' => 'decimal:4',
        ];
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(CatalogUnit::class, 'unit_id');
    }

    public function quoteLines(): HasMany
    {
        return $this->hasMany(QuoteLine::class);
    }
}
