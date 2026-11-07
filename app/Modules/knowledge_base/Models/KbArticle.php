<?php

namespace App\Modules\knowledge_base\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ImranDev\UniversalSlug\Attributes\Slug;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

#[Slug(from: 'title')]
class KbArticle extends Model
{
    use HasUniversalSlug;

    protected $table = 'kb_articles';

    protected $fillable = [
        'locale',
        'slug',
        'title',
        'category',
        'body',
        'body_format',
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
