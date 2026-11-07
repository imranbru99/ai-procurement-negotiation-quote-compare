<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\BuildsPublicSeo;
use App\Modules\cms\Services\CmsContentService;
use Illuminate\View\View;

class PageController extends Controller
{
    use BuildsPublicSeo;

    public function show(string $slug, CmsContentService $cms): View
    {
        $page = $cms->page($slug);
        abort_unless($page, 404);

        return view('public.cms-page', [
            'seo' => $this->seo(
                $page->meta_title ?? $page->title,
                $page->meta_description,
                $page->og_image,
                'Article',
            ),
            'page' => $page,
        ]);
    }
}
