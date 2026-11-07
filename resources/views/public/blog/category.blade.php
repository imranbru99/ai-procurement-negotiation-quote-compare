@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold">{{ $category->name }}</h1>
        <p class="mt-2 text-slate-600 dark:text-slate-300">{{ $category->description }}</p>
        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="text-lg font-semibold"><a href="{{ route('public.blog.show', $post->slug) }}">{{ $post->title }}</a></h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $post->excerpt }}</p>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $posts->links() }}</div>
    </section>
@endsection
