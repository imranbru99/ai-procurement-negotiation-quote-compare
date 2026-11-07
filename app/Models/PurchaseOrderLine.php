<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'award_line_id',
        'rfq_line_id',
        'description',
        'qty',
        'qty_received',
        'unit_price',
        'currency_code',
        'line_number',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:4',
            'qty_received' => 'decimal:4',
            'unit_price' => 'decimal:4',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function awardLine(): BelongsTo
    {
        return $this->belongsTo(AwardLine::class);
    }

    public function rfqLine(): BelongsTo
    {
        return $this->belongsTo(RfqLine::class);
    }
}
