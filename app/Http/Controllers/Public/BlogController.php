<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\BuildsPublicSeo;
use App\Modules\cms\Models\CmsCategory;
use App\Modules\cms\Models\CmsPost;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogController extends Controller
{
    use BuildsPublicSeo;

    public function index(Request $request): View
    {
        $query = CmsPost::query()->published()->forLocale(app()->getLocale())->latest('published_at');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        return view('public.blog.index', [
            'seo' => $this->seo(__('public.nav.blog')),
            'posts' => $query->paginate(9)->withQueryString(),
            'search' => $search ?? '',
        ]);
    }

    public function show(string $slug): View
    {
        $post = CmsPost::query()->published()->forLocale(app()->getLocale())->where('slug', $slug)->first();
        if (! $post) {
            throw new NotFoundHttpException;
        }

        return view('public.blog.show', [
            'seo' => $this->seo($post->meta_title ?? $post->title, $post->meta_description, $post->og_image, 'BlogPosting'),
            'post' => $post,
        ]);
    }

    public function category(string $slug): View
    {
        $category = CmsCategory::query()->forLocale(app()->getLocale())->where('slug', $slug)->first();
        if (! $category) {
            throw new NotFoundHttpException;
        }

        $posts = $category->posts()->published()->forLocale(app()->getLocale())->latest('published_at')->paginate(9);

        return view('public.blog.category', [
            'seo' => $this->seo($category->name, $category->meta_description),
            'category' => $category,
            'posts' => $posts,
        ]);
    }
}
