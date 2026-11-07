@extends('public.layouts.marketing')
@section('content')
    <section class="mx-auto max-w-3xl px-4 py-24 text-center lg:px-8">
        <p class="text-sm uppercase tracking-widest text-brand-700">404</p>
        <h1 class="font-display mt-2 text-4xl font-semibold">{{ __('public.errors.not_found') }}</h1>
        <a href="{{ route('public.home') }}" class="mt-8 inline-flex rounded-xl bg-brand-700 px-5 py-3 text-white">{{ __('public.errors.back_home') }}</a>
    </section>
@endsection
