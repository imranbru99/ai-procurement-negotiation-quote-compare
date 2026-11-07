<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierScorecard extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'period_start',
        'period_end',
        'on_time_score',
        'quality_score',
        'price_score',
        'responsiveness_score',
        'overall',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'on_time_score' => 'decimal:2',
            'quality_score' => 'decimal:2',
            'price_score' => 'decimal:2',
            'responsiveness_score' => 'decimal:2',
            'overall' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
