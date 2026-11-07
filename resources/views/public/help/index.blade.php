@extends('public.layouts.marketing')
@section('content')
    <section class="mx-auto max-w-6xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold">{{ __('public.nav.help') }}</h1>
        <form class="mt-6 flex gap-2" method="get"><input type="search" name="q" value="{{ $search }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 dark:border-slate-700 dark:bg-slate-900"><button class="rounded-lg bg-brand-700 px-4 py-2 text-white">{{ __('public.blog.search_btn') }}</button></form>
        <ul class="mt-10 space-y-3">
            @foreach ($articles as $article)
                <li><a class="text-brand-800 dark:text-brand-300" href="{{ route('public.help.show', $article->slug) }}">{{ $article->title }}</a></li>
            @endforeach
        </ul>
        <div class="mt-8">{{ $articles->links() }}</div>
    </section>
@endsection
