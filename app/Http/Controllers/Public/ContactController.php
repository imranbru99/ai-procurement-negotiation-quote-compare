<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\BuildsPublicSeo;
use App\Http\Requests\Public\ContactRequest;
use App\Modules\cms\Services\CmsContentService;
use App\Services\Public\ContactFormService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    use BuildsPublicSeo;

    public function create(CmsContentService $cms): View
    {
        $page = $cms->page('contact');

        return view('public.contact', [
            'seo' => $this->seo(__('public.nav.contact'), $page?->meta_description),
            'page' => $page,
        ]);
    }

    public function store(ContactRequest $request, ContactFormService $contact): RedirectResponse
    {
        $contact->send($request->validated());

        return back()->with('success', __('public.contact.success'));
    }
}
