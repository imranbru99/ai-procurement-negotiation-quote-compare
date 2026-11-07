<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\BuildsPublicSeo;
use App\Modules\cms\Services\CmsContentService;
use App\Services\Public\ModuleManifestCatalog;
use Illuminate\View\View;

class HomeController extends Controller
{
    use BuildsPublicSeo;

    public function __invoke(CmsContentService $cms, ModuleManifestCatalog $modules): View
    {
        $page = $cms->page('home');

        return view('public.home', [
            'seo' => $this->seo(
                $page?->meta_title ?? __('public.home.title'),
                $page?->meta_description ?? __('public.home.subtitle'),
                $page?->og_image,
            ),
            'page' => $page,
            'testimonials' => $cms->testimonials(),
            'posts' => $cms->recentPosts(limit: 3),
            'modules' => $modules->all()->take(8),
        ]);
    }
}
