<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierRiskAssessment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'risk_score',
        'financial_flag',
        'geo_flag',
        'compliance_flag',
        'sanctions_notes',
        'document_expiry_flags',
        'ai_reasoning',
        'assessed_at',
    ];

    protected function casts(): array
    {
        return [
            'financial_flag' => 'boolean',
            'geo_flag' => 'boolean',
            'compliance_flag' => 'boolean',
            'document_expiry_flags' => 'array',
            'assessed_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
