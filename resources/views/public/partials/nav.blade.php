@php
    $headerMenus = collect();
    if (\Illuminate\Support\Facades\Schema::hasTable('cms_menus')) {
        $headerMenus = app(\App\Modules\cms\Services\CmsContentService::class)->menus('header', $publicLocale ?? null);
    }
@endphp
<header class="sticky top-0 z-40 border-b border-slate-200/60 bg-white/80 backdrop-blur-lg dark:border-slate-800/80 dark:bg-slate-950/80">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 lg:px-8">
        <a href="{{ route('public.home') }}" class="group flex flex-col">
            <span class="font-display text-xl font-semibold tracking-tight text-brand-800 dark:text-brand-200">{{ config('public.brand.name') }}</span>
            <span class="text-xs text-slate-500 dark:text-slate-400">{{ config('public.brand.tagline') }}</span>
        </a>

        <nav class="hidden items-center gap-6 text-sm font-medium lg:flex">
            @forelse ($headerMenus as $item)
                <a href="{{ $item->url }}" class="text-slate-600 transition hover:text-brand-700 dark:text-slate-300 dark:hover:text-brand-300">{{ $item->label }}</a>
            @empty
                <a href="{{ route('public.features') }}" class="text-slate-600 hover:text-brand-700 dark:text-slate-300">{{ __('public.nav.features') }}</a>
                <a href="{{ route('public.pricing') }}" class="text-slate-600 hover:text-brand-700 dark:text-slate-300">{{ __('public.nav.pricing') }}</a>
                <a href="{{ route('public.demo') }}" class="text-slate-600 hover:text-brand-700 dark:text-slate-300">{{ __('public.nav.demo') }}</a>
                <a href="{{ route('public.blog.index') }}" class="text-slate-600 hover:text-brand-700 dark:text-slate-300">{{ __('public.nav.blog') }}</a>
            @endforelse
        </nav>

        <div class="flex items-center gap-2">
            <select
                aria-label="{{ __('public.nav.language') }}"
                class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900"
                onchange="window.location.href='{{ url()->current() }}?locale=' + this.value"
            >
                @foreach ($publicLocales ?? config('public.locales', []) as $code => $meta)
                    <option value="{{ $code }}" @selected(($publicLocale ?? app()->getLocale()) === $code)>{{ $meta['name'] ?? $code }}</option>
                @endforeach
            </select>
            <button type="button" @click="toggle()" class="rounded-lg border border-slate-200 px-2 py-1 text-xs dark:border-slate-700" :aria-pressed="dark">
                <span x-show="!dark">☀</span>
                <span x-show="dark">☾</span>
            </button>
            <a href="{{ route('public.demo') }}" class="hidden rounded-xl bg-brand-700 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-brand-900/20 transition hover:bg-brand-600 sm:inline-flex">{{ __('public.cta.try_demo') }}</a>
        </div>
    </div>
</header>
