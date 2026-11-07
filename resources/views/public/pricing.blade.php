@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-16 lg:px-8" x-data="pricingToggle()" data-currencies='@json($currencies)'>
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="font-display text-4xl font-semibold">{{ __('public.nav.pricing') }}</h1>
                <p class="mt-3 max-w-2xl text-slate-600 dark:text-slate-300">{{ $page?->excerpt ?? __('public.pricing.lead') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <select x-model="currency" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @foreach ($currencies as $code => $meta)
                        <option value="{{ $code }}">{{ $code }}</option>
                    @endforeach
                </select>
                <button type="button" @click="yearly = !yearly" class="rounded-full border border-slate-300 px-4 py-2 text-sm dark:border-slate-700" x-text="yearly ? '{{ __('public.pricing.billed_yearly') }}' : '{{ __('public.pricing.billed_monthly') }}'"></button>
            </div>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-3">
            @foreach ($plans as $key => $plan)
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="text-xl font-semibold">{{ $plan['name'] }}</h2>
                    <p class="mt-4 font-display text-4xl font-semibold text-brand-700 dark:text-brand-300">
                        <span x-text="symbol()"></span><span x-text="price({{ $plan['monthly'] }}, {{ $plan['yearly'] }})"></span>
                        <span class="text-base font-normal text-slate-500" x-text="yearly ? '/yr' : '/mo'"></span>
                    </p>
                    <ul class="mt-6 space-y-2 text-sm text-slate-600 dark:text-slate-300">
                        @foreach ($plan['modules'] as $module)
                            <li>• {{ $module === 'all' ? __('public.pricing.all_modules') : $module }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="mt-16">
            <h2 class="font-display text-2xl font-semibold">{{ __('public.pricing.faq') }}</h2>
            <div class="mt-6 space-y-4">
                @forelse ($faqs as $faq)
                    <details class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                        <summary class="cursor-pointer font-medium">{{ $faq->question }}</summary>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $faq->answer }}</p>
                    </details>
                @empty
                    <p class="text-slate-600 dark:text-slate-300">{{ __('public.pricing.faq_empty') }}</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
