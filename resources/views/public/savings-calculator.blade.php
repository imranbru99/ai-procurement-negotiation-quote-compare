@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-16 lg:px-8" x-data="savingsCalculator()">
        <h1 class="font-display text-4xl font-semibold">{{ __('public.nav.savings_calculator') }}</h1>
        @include('public.partials.cms-body', ['page' => $page, 'fallback' => __('public.savings.lead')])
        <div class="mt-8 space-y-6 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
            <label class="block text-sm font-medium">{{ __('public.savings.annual_spend') }}
                <input type="range" min="50000" max="5000000" step="10000" x-model.number="spend" class="mt-2 w-full">
                <span class="text-brand-700 dark:text-brand-300" x-text="'$' + spend.toLocaleString()"></span>
            </label>
            <label class="block text-sm font-medium">{{ __('public.savings.suppliers') }}
                <input type="number" x-model.number="suppliers" min="1" class="mt-2 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 dark:border-slate-700">
            </label>
            <label class="block text-sm font-medium">{{ __('public.savings.rfqs') }}
                <input type="number" x-model.number="rfqs" min="1" class="mt-2 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 dark:border-slate-700">
            </label>
            <p class="font-display text-3xl font-semibold text-brand-800 dark:text-brand-200">
                {{ __('public.savings.estimated') }} <span x-text="'$' + estimatedSavings.toLocaleString()"></span>
            </p>
        </div>
    </section>
@endsection
