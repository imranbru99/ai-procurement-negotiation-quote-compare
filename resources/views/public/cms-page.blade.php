@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold text-slate-900 dark:text-white">{{ $page->title }}</h1>
        <div class="mt-8">
            @include('public.partials.cms-body', ['page' => $page])
        </div>
    </section>
@endsection
