<?php

namespace App\Modules\catalog\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Modules\catalog\Database\Factories\CatalogItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ImranDev\UniversalSlug\Attributes\Slug;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Slug(from: 'name')]
class CatalogItem extends Model
{
    /** @use HasFactory<CatalogItemFactory> */
    use BelongsToTenant;

    use HasFactory;
    use HasUniversalSlug;
    use LogsActivity;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'sku',
        'name',
        'slug',
        'description',
        'unit_id',
        'specs',
        'base_price',
        'currency_code',
        'is_service',
        'is_active',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'specs' => 'array',
            'translations' => 'array',
            'base_price' => 'decimal:4',
            'is_service' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CatalogCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(CatalogUnit::class, 'unit_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['sku', 'name', 'base_price', 'is_active'])->logOnlyDirty();
    }

    protected static function newFactory(): CatalogItemFactory
    {
        return CatalogItemFactory::new();
    }
}
