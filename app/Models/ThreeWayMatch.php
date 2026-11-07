<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreeWayMatch extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'three_way_matches';

    protected $fillable = [
        'tenant_id',
        'po_id',
        'grn_id',
        'invoice_id',
        'status',
        'variance_amount',
        'tolerance_percent',
        'exception_reason',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'variance_amount' => 'decimal:2',
            'tolerance_percent' => 'decimal:2',
            'resolved_at' => 'datetime',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class, 'grn_id');
    }

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class, 'invoice_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
