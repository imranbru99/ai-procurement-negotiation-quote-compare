<?php

namespace App\Modules\suppliers\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Modules\suppliers\Database\Factories\SupplierCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Slug(from: 'name')]
class SupplierCategory extends Model
{
    /** @use HasFactory<SupplierCategoryFactory> */
    use BelongsToTenant;

    use HasFactory;
    use HasUniversalSlug;
    use LogsActivity;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
    ];

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class, 'category_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'slug', 'description'])->logOnlyDirty();
    }

    protected static function newFactory(): SupplierCategoryFactory
    {
        return SupplierCategoryFactory::new();
    }
}
