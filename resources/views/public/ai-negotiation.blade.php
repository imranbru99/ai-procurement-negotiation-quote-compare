@extends('public.layouts.marketing')
@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold text-slate-900 dark:text-white">{{ $seo['jsonLd']['headline'] ?? '' }}</h1>
        <div class="mt-6">@include('public.partials.cms-body', ['page' => $page ?? null, 'fallback' => null])</div>
    </section>
@endsection
