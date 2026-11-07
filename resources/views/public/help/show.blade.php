@extends('public.layouts.marketing')
@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold">{{ $article->title }}</h1>
        <div class="prose-public mt-8 dark:prose-invert">{!! $article->renderedBody() !!}</div>
    </article>
@endsection
