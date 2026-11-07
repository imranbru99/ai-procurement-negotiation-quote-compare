@extends('public.layouts.marketing')

@section('content')
    <section class="hero-grid relative overflow-hidden text-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-20 lg:grid-cols-2 lg:px-8 lg:py-28">
            <div class="animate-float-in space-y-6">
                <p class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] text-brand-100">{{ __('public.home.eyebrow') }}</p>
                <h1 class="font-display text-4xl font-semibold leading-tight md:text-5xl">{{ $page?->title ?? __('public.home.title') }}</h1>
                <p class="max-w-xl text-lg text-slate-200">{{ __('public.home.subtitle') }}</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('public.demo') }}" class="rounded-xl bg-brand-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-brand-400">{{ __('public.cta.try_demo') }}</a>
                    <a href="{{ route('public.quote_comparison') }}" class="rounded-xl border border-white/30 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">{{ __('public.cta.quote_demo') }}</a>
                </div>
            </div>
            <div class="animate-float-in rounded-3xl border border-white/15 bg-white/5 p-6 backdrop-blur" style="animation-delay: 120ms">
                @include('public.partials.cms-body', ['page' => $page, 'fallback' => __('public.home.panel')])
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
        <h2 class="font-display text-3xl font-semibold text-slate-900 dark:text-white">{{ __('public.home.modules_heading') }}</h2>
        <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($modules as $module)
                <a href="{{ route('public.features.show', $module['key']) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-400 dark:border-slate-800 dark:bg-slate-900">
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $module['name'] }}</p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($module['description'], 96) }}</p>
                </a>
            @endforeach
        </div>
    </section>

    @if ($testimonials->isNotEmpty())
        <section class="bg-slate-100 py-16 dark:bg-slate-900/50">
            <div class="mx-auto max-w-7xl px-4 lg:px-8">
                <h2 class="font-display text-3xl font-semibold">{{ __('public.home.testimonials') }}</h2>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    @foreach ($testimonials as $item)
                        <blockquote class="rounded-2xl bg-white p-6 shadow-sm dark:bg-slate-900">
                            <p class="text-slate-700 dark:text-slate-300">“{{ $item->body }}”</p>
                            <footer class="mt-4 text-sm font-semibold text-brand-800 dark:text-brand-300">{{ $item->name }} · {{ $item->company }}</footer>
                        </blockquote>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
