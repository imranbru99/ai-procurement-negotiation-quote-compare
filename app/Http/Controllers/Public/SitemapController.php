<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Modules\cms\Models\CmsPage;
use App\Modules\cms\Models\CmsPost;
use App\Modules\knowledge_base\Models\KbArticle;
use App\Services\Public\ModuleManifestCatalog;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(ModuleManifestCatalog $modules): Response
    {
        $urls = collect([
            url('/'),
            route('public.features'),
            route('public.pricing'),
            route('public.demo'),
            route('public.blog.index'),
            route('public.help.index'),
            route('public.contact.create'),
        ]);

        foreach ($modules->all() as $module) {
            $urls->push(route('public.features.show', $module['key']));
        }

        foreach (config('public.industries', []) as $industry) {
            $urls->push(route('public.solutions.industry', $industry));
        }

        foreach (['terms', 'privacy', 'cookie', 'dpa'] as $legal) {
            $urls->push(route('public.legal', $legal));
        }

        CmsPage::query()->published()->pluck('slug')->each(function (string $slug) use ($urls): void {
            if (! in_array($slug, ['home'], true)) {
                $urls->push(route('public.page', $slug));
            }
        });

        CmsPost::query()->published()->get(['slug'])->each(
            fn (CmsPost $post) => $urls->push(route('public.blog.show', $post->slug))
        );

        KbArticle::query()->published()->get(['slug'])->each(
            fn (KbArticle $article) => $urls->push(route('public.help.show', $article->slug))
        );

        $xml = view('public.sitemap', ['urls' => $urls->unique()->values()])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
