<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiptLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_id',
        'po_line_id',
        'qty_received',
        'qty_accepted',
        'qty_rejected',
        'qc_status',
        'discrepancy_notes',
    ];

    protected function casts(): array
    {
        return [
            'qty_received' => 'decimal:4',
            'qty_accepted' => 'decimal:4',
            'qty_rejected' => 'decimal:4',
        ];
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class, 'grn_id');
    }

    public function purchaseOrderLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderLine::class, 'po_line_id');
    }
}
