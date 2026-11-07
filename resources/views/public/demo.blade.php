@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold">{{ __('public.nav.demo') }}</h1>
        <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">@include('public.partials.cms-body', ['page' => $page, 'fallback' => __('public.demo.lead')])</p>

        <div class="mt-10 grid gap-4 md:grid-cols-2">
            @foreach ($accounts as $account)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="text-lg font-semibold">{{ $account['role'] }}</h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $account['description'] }}</p>
                    <dl class="mt-4 space-y-1 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Email</dt><dd class="font-mono">{{ $account['email'] }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Password</dt><dd class="font-mono">{{ $account['password_hint'] }}</dd></div>
                    </dl>
                    <a href="{{ url($account['login_url']) }}" class="mt-5 inline-flex rounded-xl bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">{{ __('public.demo.login') }}</a>
                </div>
            @endforeach
        </div>
    </section>
@endsection
