<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\BuildsPublicSeo;
use App\Modules\knowledge_base\Models\KbArticle;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HelpCenterController extends Controller
{
    use BuildsPublicSeo;

    public function index(Request $request): View
    {
        $query = KbArticle::query()->published()->forLocale(app()->getLocale())->latest('published_at');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        return view('public.help.index', [
            'seo' => $this->seo(__('public.nav.help')),
            'articles' => $query->paginate(12)->withQueryString(),
            'search' => $search ?? '',
        ]);
    }

    public function show(string $slug): View
    {
        $article = KbArticle::query()->published()->forLocale(app()->getLocale())->where('slug', $slug)->first();
        if (! $article) {
            throw new NotFoundHttpException;
        }

        return view('public.help.show', [
            'seo' => $this->seo($article->meta_title ?? $article->title, $article->meta_description, $article->og_image, 'TechArticle'),
            'article' => $article,
        ]);
    }
}
