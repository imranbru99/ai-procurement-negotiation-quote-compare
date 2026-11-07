@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold">{{ __('public.nav.contact') }}</h1>
        <div class="mt-4">@include('public.partials.cms-body', ['page' => $page])</div>

        @if (session('success'))
            <p class="mt-6 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900 dark:bg-brand-900/40 dark:text-brand-100">{{ session('success') }}</p>
        @endif

        <form action="{{ route('public.contact.store') }}" method="post" class="mt-10 space-y-4">
            @csrf
            <label class="block text-sm font-medium">Name
                <input name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
            </label>
            <label class="block text-sm font-medium">Email
                <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
            </label>
            <label class="block text-sm font-medium">Company
                <input name="company" value="{{ old('company') }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
            </label>
            <label class="block text-sm font-medium">Message
                <textarea name="message" rows="5" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">{{ old('message') }}</textarea>
            </label>
            <button type="submit" class="rounded-xl bg-brand-700 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-600">{{ __('public.contact.submit') }}</button>
        </form>
    </section>
@endsection
