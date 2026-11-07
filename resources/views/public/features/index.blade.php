@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold">{{ __('public.nav.features') }}</h1>
        <div class="mt-4">@include('public.partials.cms-body', ['page' => $page, 'fallback' => __('public.features.lead')])</div>

        @module('quote_comparison')
            <p class="mt-6 text-sm text-brand-700 dark:text-brand-300">{{ __('public.features.quote_comparison_enabled') }}</p>
        @else
            <p class="mt-6 text-sm text-slate-500">{{ __('public.features.quote_comparison_demo_hint') }}</p>
        @endmodule

        <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($modules as $module)
                <a href="{{ route('public.features.show', $module['key']) }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-brand-500 dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="text-lg font-semibold">{{ $module['name'] }}</h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $module['description'] }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endsection
