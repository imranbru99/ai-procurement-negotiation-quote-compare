@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold text-slate-900 dark:text-white">{{ $seo['jsonLd']['headline'] ?? __('public.pages.supplier-signup.title') }}</h1>
        <div class="mt-6">@include('public.partials.cms-body', ['page' => $page ?? null, 'fallback' => __('public.pages.supplier-signup.description')])</div>

        @if (session('success'))
            <p class="mt-6 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900 dark:bg-brand-900/40 dark:text-brand-100">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-100">
                <ul class="list-disc space-y-1 ps-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('public.supplier_signup.store') }}" method="post" class="mt-10 space-y-4">
            @csrf

            <label class="block text-sm font-medium">{{ __('public.supplier_signup.company_name') }}
                <input name="company_name" value="{{ old('company_name') }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
            </label>

            <label class="block text-sm font-medium">{{ __('public.supplier_signup.contact_name') }}
                <input name="contact_name" value="{{ old('contact_name') }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
            </label>

            <label class="block text-sm font-medium">{{ __('public.supplier_signup.email') }}
                <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
            </label>

            <label class="block text-sm font-medium">{{ __('public.supplier_signup.phone') }}
                <input name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
            </label>

            <label class="block text-sm font-medium">{{ __('public.supplier_signup.country') }}
                <input name="country" value="{{ old('country') }}" maxlength="2" placeholder="US" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
            </label>

            @if (($tenants ?? collect())->isNotEmpty())
                <label class="block text-sm font-medium">{{ __('public.supplier_signup.tenant') }}
                    <select name="tenant_slug" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
                        <option value="">{{ __('public.supplier_signup.tenant_default') }}</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->slug }}" @selected(old('tenant_slug') === $tenant->slug)>{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </label>
            @endif

            <label class="block text-sm font-medium">{{ __('public.supplier_signup.password') }}
                <input type="password" name="password" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
            </label>

            <label class="block text-sm font-medium">{{ __('public.supplier_signup.password_confirmation') }}
                <input type="password" name="password_confirmation" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
            </label>

            <label class="block text-sm font-medium">{{ __('public.supplier_signup.notes') }}
                <textarea name="notes" rows="4" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">{{ old('notes') }}</textarea>
            </label>

            <button type="submit" class="rounded-xl bg-brand-700 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-600">{{ __('public.supplier_signup.submit') }}</button>
        </form>
    </section>
@endsection
