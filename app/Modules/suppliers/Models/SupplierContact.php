<?php

namespace App\Modules\suppliers\Models;

use App\Modules\suppliers\Database\Factories\SupplierContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierContact extends Model
{
    /** @use HasFactory<SupplierContactFactory> */
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
        'email',
        'phone',
        'role',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    protected static function newFactory(): SupplierContactFactory
    {
        return SupplierContactFactory::new();
    }
}
