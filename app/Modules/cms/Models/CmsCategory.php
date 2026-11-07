<?php

namespace App\Modules\cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

#[Slug(from: 'name')]
class CmsCategory extends Model
{
    use HasUniversalSlug;

    protected $table = 'cms_categories';

    protected $fillable = [
        'locale',
        'slug',
        'name',
        'description',
        'meta_title',
        'meta_description',
        'og_image',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(CmsPost::class, 'cms_category_id');
    }

    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }
}
