@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-16 lg:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <h1 class="font-display text-4xl font-semibold">{{ __('public.nav.blog') }}</h1>
            <form action="{{ route('public.blog.index') }}" method="get" class="flex gap-2">
                <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('public.blog.search') }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <button class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white">{{ __('public.blog.search_btn') }}</button>
            </form>
        </div>
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
