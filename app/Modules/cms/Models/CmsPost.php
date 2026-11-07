<?php

namespace App\Modules\cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ImranDev\UniversalSlug\Attributes\Slug;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

#[Slug(from: 'title')]
class CmsPost extends Model
{
    use HasUniversalSlug;

    protected $table = 'cms_posts';

    protected $fillable = [
        'cms_category_id',
        'locale',
        'slug',
        'title',
        'excerpt',
        'body',
        'body_format',
        'author_name',
        'meta_title',
        'meta_description',
        'og_image',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsCategory::class, 'cms_category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }

    public function renderedBody(): string
    {
        if ($this->body_format === 'markdown' && $this->body) {
            return str((string) $this->body)->markdown()->sanitizeHtml();
        }

        return (string) ($this->body ?? '');
    }
}
