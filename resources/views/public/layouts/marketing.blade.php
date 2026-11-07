<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $publicLocale ?? app()->getLocale()) }}" dir="{{ ($publicRtl ?? false) ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seo['title'] ?? config('public.brand.name') }}</title>
    <meta name="description" content="{{ $seo['description'] ?? __('public.seo.default_description') }}">
    @if (! empty($seo['image']))
        <meta property="og:image" content="{{ $seo['image'] }}">
    @endif
    <meta property="og:title" content="{{ $seo['title'] ?? config('public.brand.name') }}">
    <meta property="og:description" content="{{ $seo['description'] ?? __('public.seo.default_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700|source-sans-3:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">@json($seo['jsonLd'] ?? [])</script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100" x-data="themeToggle()">
    <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-brand-600 focus:px-3 focus:py-2 focus:text-white">{{ __('public.a11y.skip') }}</a>

    @include('public.partials.nav')

    <main id="content">
        @yield('content')
    </main>

    @include('public.partials.footer')
</body>
</html>
