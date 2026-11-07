<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'cost_center_id',
        'year',
        'amount',
        'currency_code',
        'committed',
        'actual',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'committed' => 'decimal:2',
            'actual' => 'decimal:2',
        ];
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function remaining(): string
    {
        return (string) ($this->amount - $this->committed - $this->actual);
    }

    public function isExceeded(): bool
    {
        return (float) ($this->committed + $this->actual) > (float) $this->amount;
    }
}
