<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\BuildsPublicSeo;
use App\Modules\cms\Services\CmsContentService;
use App\Services\Public\ModuleManifestCatalog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FeatureController extends Controller
{
    use BuildsPublicSeo;

    public function index(ModuleManifestCatalog $catalog, CmsContentService $cms): View
    {
        $page = $cms->page('features');

        return view('public.features.index', [
            'seo' => $this->seo(__('public.nav.features'), $page?->meta_description),
            'page' => $page,
            'modules' => $catalog->all(),
        ]);
    }

    public function show(string $module, ModuleManifestCatalog $catalog, CmsContentService $cms, Request $request): View
    {
        $manifest = $catalog->find($module);
        if (! $manifest) {
            throw new NotFoundHttpException;
        }

        $page = $cms->page('feature-'.$module);

        return view('public.features.show', [
            'seo' => $this->seo(
                $page?->meta_title ?? $manifest['name'],
                $page?->meta_description ?? $manifest['description'],
            ),
            'manifest' => $manifest,
            'page' => $page,
        ]);
    }
}
