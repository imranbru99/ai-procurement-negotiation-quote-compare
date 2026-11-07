<?php

namespace App\Modules\cms\Services;

use App\Modules\cms\Models\CmsFaq;
use App\Modules\cms\Models\CmsMenu;
use App\Modules\cms\Models\CmsPage;
use App\Modules\cms\Models\CmsPost;
use App\Modules\cms\Models\CmsTestimonial;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CmsContentService
{
    public function page(string $slug, ?string $locale = null): ?CmsPage
    {
        if (! Schema::hasTable('cms_pages')) {
            return null;
        }

        $locale ??= app()->getLocale();

        return CmsPage::query()
            ->published()
            ->forLocale($locale)
            ->where('slug', $slug)
            ->first()
            ?? CmsPage::query()
                ->published()
                ->forLocale(config('public.default_locale', 'en'))
                ->where('slug', $slug)
                ->first();
    }

    /**
     * @return Collection<int, CmsPost>
     */
    public function recentPosts(?string $locale = null, int $limit = 6): Collection
    {
        if (! Schema::hasTable('cms_posts')) {
            return collect();
        }

        $locale ??= app()->getLocale();

        return CmsPost::query()
            ->published()
            ->forLocale($locale)
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, CmsFaq>
     */
    public function faqs(?string $category = null, ?string $locale = null): Collection
    {
        $locale ??= app()->getLocale();

        return CmsFaq::query()
            ->published()
            ->forLocale($locale)
            ->when($category, fn ($q) => $q->where('category', $category))
            ->orderBy('sort')
            ->get();
    }

    /**
     * @return Collection<int, CmsTestimonial>
     */
    public function testimonials(?string $locale = null): Collection
    {
        if (! Schema::hasTable('cms_testimonials')) {
            return collect();
        }

        $locale ??= app()->getLocale();

        return CmsTestimonial::query()
            ->published()
            ->forLocale($locale)
            ->latest('published_at')
            ->get();
    }

    /**
     * @return Collection<int, CmsMenu>
     */
    public function menus(string $location, ?string $locale = null): Collection
    {
        $locale ??= app()->getLocale();

        return CmsMenu::query()
            ->forLocale($locale)
            ->where('location', $location)
            ->whereNull('parent_id')
            ->orderBy('sort')
            ->with('children')
            ->get();
    }
}
