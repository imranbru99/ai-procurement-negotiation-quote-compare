@extends('public.layouts.marketing')
@section('content')
    <section class="mx-auto max-w-4xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold">{{ __('public.solutions.'.$industry.'.title') }}</h1>
        <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">{{ __('public.solutions.'.$industry.'.lead') }}</p>
        <div class="mt-8">@include('public.partials.cms-body', ['page' => $page])</div>
    </section>
@endsection
