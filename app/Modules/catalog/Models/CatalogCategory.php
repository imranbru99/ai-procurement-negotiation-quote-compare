<?php

namespace App\Modules\catalog\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Modules\catalog\Database\Factories\CatalogCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Slug(from: 'name')]
class CatalogCategory extends Model
{
    /** @use HasFactory<CatalogCategoryFactory> */
    use BelongsToTenant;

    use HasFactory;
    use HasUniversalSlug;
    use LogsActivity;

    protected $table = 'catalog_categories';

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'slug',
        'path',
    ];

    protected static function booted(): void
    {
        static::saved(function (CatalogCategory $category): void {
            $parentPath = $category->parent_id
                ? CatalogCategory::query()->withoutGlobalScopes()->find($category->parent_id)?->path
                : null;

            $path = $parentPath
                ? rtrim($parentPath, '/').'/'.$category->slug
                : '/'.$category->slug;

            if ($category->path !== $path) {
                $category->forceFill(['path' => $path])->saveQuietly();
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CatalogItem::class, 'category_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'slug', 'parent_id'])->logOnlyDirty();
    }

    protected static function newFactory(): CatalogCategoryFactory
    {
        return CatalogCategoryFactory::new();
    }
}
