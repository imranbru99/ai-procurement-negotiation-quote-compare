@php
    $seo = $seo ?? [
        'title' => '500 — '.config('public.brand.name'),
        'description' => __('public.errors.server_error'),
        'image' => null,
        'jsonLd' => [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Server error',
        ],
    ];
    $publicLocale = $publicLocale ?? app()->getLocale();
    $publicLocales = $publicLocales ?? config('public.locales', []);
    $publicRtl = $publicRtl ?? (bool) data_get($publicLocales, $publicLocale.'.rtl', false);
@endphp
@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-24 text-center lg:px-8">
        <p class="text-sm uppercase tracking-widest text-brand-700">500</p>
        <h1 class="font-display mt-2 text-4xl font-semibold">{{ __('public.errors.server_error') }}</h1>
        <a href="{{ url('/') }}" class="mt-8 inline-flex rounded-xl bg-brand-700 px-5 py-3 text-white">{{ __('public.errors.back_home') }}</a>
    </section>
@endsection
