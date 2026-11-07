@extends('public.layouts.marketing')
@section('content')
    <section class="mx-auto max-w-6xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold">{{ __('public.nav.solutions') }}</h1>
        <div class="mt-4">@include('public.partials.cms-body', ['page' => $page, 'fallback' => __('public.solutions.lead')])</div>
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($industries as $industry)
                <a href="{{ route('public.solutions.industry', $industry) }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-brand-500 dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="font-semibold">{{ __('public.solutions.'.$industry.'.title') }}</h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ __('public.solutions.'.$industry.'.lead') }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endsection
