@extends('public.layouts.marketing')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 lg:px-8">
        <p class="text-sm text-slate-500">{{ optional($post->published_at)->format('M j, Y') }} · {{ $post->author_name }}</p>
        <h1 class="font-display mt-2 text-4xl font-semibold">{{ $post->title }}</h1>
        <div class="prose-public mt-8 dark:prose-invert">{!! $post->renderedBody() !!}</div>
    </article>
@endsection
