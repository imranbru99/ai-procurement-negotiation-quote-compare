<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\BuildsPublicSeo;
use App\Modules\cms\Services\CmsContentService;
use App\Services\Public\ModuleManifestCatalog;
use Illuminate\View\View;

class PricingController extends Controller
{
    use BuildsPublicSeo;

    public function __invoke(CmsContentService $cms, ModuleManifestCatalog $modules): View
    {
        $page = $cms->page('pricing');

        return view('public.pricing', [
            'seo' => $this->seo(__('public.nav.pricing'), $page?->meta_description),
            'page' => $page,
            'plans' => config('public.pricing.plans'),
            'currencies' => config('public.pricing.currencies'),
            'faqs' => $cms->faqs('pricing'),
            'modules' => $modules->all(),
        ]);
    }
}
