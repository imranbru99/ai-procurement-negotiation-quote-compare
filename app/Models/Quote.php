<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasComments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    use BelongsToTenant;
    use HasComments;
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'rfq_id',
        'supplier_id',
        'number',
        'status',
        'currency_code',
        'subtotal',
        'tax_total',
        'shipping_total',
        'other_fees',
        'grand_total',
        'lead_time_days',
        'payment_terms',
        'warranty_terms',
        'moq',
        'valid_until',
        'notes',
        'submitted_at',
        'source',
        'exchange_rate_to_base',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'shipping_total' => 'decimal:2',
            'other_fees' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'moq' => 'decimal:4',
            'valid_until' => 'date',
            'submitted_at' => 'datetime',
            'exchange_rate_to_base' => 'decimal:6',
        ];
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(QuoteLine::class);
    }

    public function tcoAmount(): float
    {
        if ($this->grand_total > 0) {
            return (float) $this->grand_total;
        }

        return (float) $this->subtotal
            + (float) $this->tax_total
            + (float) $this->shipping_total
            + (float) $this->other_fees;
    }

    public function paymentTermsScore(): int
    {
        $terms = strtolower((string) $this->payment_terms);

        return match (true) {
            str_contains($terms, 'prepaid'), str_contains($terms, 'advance') => 90,
            str_contains($terms, 'net 15') => 80,
            str_contains($terms, 'net 30') => 70,
            str_contains($terms, 'net 45') => 60,
            str_contains($terms, 'net 60') => 50,
            str_contains($terms, 'net 90') => 40,
            default => 55,
        };
    }

    public function warrantyMonths(): int
    {
        if (preg_match('/(\d+)\s*(month|mo)/i', (string) $this->warranty_terms, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/(\d+)\s*year/i', (string) $this->warranty_terms, $matches)) {
            return (int) $matches[1] * 12;
        }

        return 12;
    }

    public function supplierTierScore(): int
    {
        $tier = strtolower((string) ($this->supplier?->tier?->value ?? $this->supplier?->tier ?? 'standard'));

        return match ($tier) {
            'strategic', 'platinum' => 5,
            'preferred', 'gold' => 4,
            'standard', 'silver' => 3,
            'probation', 'bronze' => 2,
            default => 3,
        };
    }
}
