@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-16 lg:px-8">
        <p class="text-xs uppercase tracking-widest text-brand-700 dark:text-brand-300">{{ $manifest['category'] }}</p>
        <h1 class="font-display mt-2 text-4xl font-semibold">{{ $manifest['name'] }}</h1>
        <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">{{ $manifest['description'] }}</p>
        <div class="mt-8">@include('public.partials.cms-body', ['page' => $page, 'fallback' => __('public.features.module_fallback')])</div>
    </section>
@endsection
