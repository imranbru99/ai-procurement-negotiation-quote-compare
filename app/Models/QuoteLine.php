<?php

namespace App\Models;

use App\Modules\catalog\Models\CatalogUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'rfq_line_id',
        'qty',
        'unit_price',
        'description',
        'unit_id',
        'tax_rate',
        'line_total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:4',
            'unit_price' => 'decimal:4',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function rfqLine(): BelongsTo
    {
        return $this->belongsTo(RfqLine::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(CatalogUnit::class, 'unit_id');
    }
}
