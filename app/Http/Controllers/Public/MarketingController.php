<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\BuildsPublicSeo;
use App\Http\Requests\Public\SupplierSignupRequest;
use App\Models\Tenant;
use App\Modules\cms\Services\CmsContentService;
use App\Services\Public\SupplierSignupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MarketingController extends Controller
{
    use BuildsPublicSeo;

    public function aiNegotiation(CmsContentService $cms): View
    {
        return $this->render('ai-negotiation', 'public.ai-negotiation', $cms);
    }

    public function quoteComparison(): View
    {
        return view('public.quote-comparison-demo', [
            'seo' => $this->seo(__('public.nav.quote_comparison')),
            'demo' => config('public.quote_comparison_demo'),
        ]);
    }

    public function howItWorks(CmsContentService $cms): View
    {
        return $this->render('how-it-works', 'public.how-it-works', $cms);
    }

    public function solutionsIndex(CmsContentService $cms): View
    {
        return view('public.solutions.index', [
            'seo' => $this->seo(__('public.nav.solutions'), $cms->page('solutions')?->meta_description),
            'industries' => config('public.industries'),
            'page' => $cms->page('solutions'),
        ]);
    }

    public function solutionIndustry(string $industry, CmsContentService $cms): View
    {
        if (! in_array($industry, config('public.industries', []), true)) {
            throw new NotFoundHttpException;
        }

        $page = $cms->page('solution-'.$industry);

        return view('public.solutions.show', [
            'seo' => $this->seo(
                __('public.solutions.'.$industry.'.title'),
                $page?->meta_description ?? __('public.solutions.'.$industry.'.lead'),
            ),
            'industry' => $industry,
            'page' => $page,
        ]);
    }

    public function savingsCalculator(CmsContentService $cms): View
    {
        return $this->render('savings-calculator', 'public.savings-calculator', $cms);
    }

    public function demo(CmsContentService $cms): View
    {
        return view('public.demo', [
            'seo' => $this->seo(__('public.nav.demo'), $cms->page('demo')?->meta_description),
            'accounts' => config('public.demo_accounts'),
            'page' => $cms->page('demo'),
        ]);
    }

    public function supplierSignup(CmsContentService $cms): View
    {
        $page = $cms->page('supplier-signup');

        return view('public.supplier-signup', [
            'seo' => $this->seo(
                $page?->meta_title ?? __('public.pages.supplier-signup.title'),
                $page?->meta_description ?? __('public.pages.supplier-signup.description'),
                $page?->og_image,
            ),
            'page' => $page,
            'tenants' => Tenant::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function storeSupplierSignup(SupplierSignupRequest $request, SupplierSignupService $signup): RedirectResponse
    {
        try {
            $signup->register($request->validated());
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['email' => $exception->getMessage()]);
        }

        return back()->with('success', __('public.supplier_signup.success'));
    }

    public function integrations(CmsContentService $cms): View
    {
        return $this->render('integrations', 'public.integrations', $cms);
    }

    public function security(CmsContentService $cms): View
    {
        return $this->render('security', 'public.security', $cms);
    }

    public function changelog(CmsContentService $cms): View
    {
        return $this->render('changelog', 'public.changelog', $cms);
    }

    public function about(CmsContentService $cms): View
    {
        return $this->render('about', 'public.about', $cms);
    }

    public function careers(CmsContentService $cms): View
    {
        return $this->render('careers', 'public.careers', $cms);
    }

    public function partners(CmsContentService $cms): View
    {
        return $this->render('partners', 'public.partners', $cms);
    }

    public function legal(string $document, CmsContentService $cms): View
    {
        $allowed = ['terms', 'privacy', 'cookie', 'dpa'];
        if (! in_array($document, $allowed, true)) {
            throw new NotFoundHttpException;
        }

        $page = $cms->page($document);
        abort_unless($page, 404);

        return view('public.cms-page', [
            'seo' => $this->seo($page->meta_title ?? $page->title, $page->meta_description, $page->og_image, 'WebPage'),
            'page' => $page,
        ]);
    }

    protected function render(string $slug, string $view, CmsContentService $cms): View
    {
        $page = $cms->page($slug);

        return view($view, [
            'seo' => $this->seo(
                $page?->meta_title ?? __("public.pages.{$slug}.title"),
                $page?->meta_description ?? __("public.pages.{$slug}.description"),
                $page?->og_image,
            ),
            'page' => $page,
        ]);
    }
}
