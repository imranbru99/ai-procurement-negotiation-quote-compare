<footer class="border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 text-sm lg:grid-cols-4 lg:px-8">
        <div class="lg:col-span-2">
            <p class="font-display text-lg font-semibold text-brand-800 dark:text-brand-200">{{ config('public.brand.name') }}</p>
            <p class="mt-2 max-w-md text-slate-600 dark:text-slate-400">{{ __('public.footer.blurb') }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-900 dark:text-slate-100">{{ __('public.footer.product') }}</p>
            <ul class="mt-3 space-y-2 text-slate-600 dark:text-slate-400">
                <li><a href="{{ route('public.features') }}">{{ __('public.nav.features') }}</a></li>
                <li><a href="{{ route('public.pricing') }}">{{ __('public.nav.pricing') }}</a></li>
                <li><a href="{{ route('public.security') }}">{{ __('public.nav.security') }}</a></li>
            </ul>
        </div>
        <div>
            <p class="font-semibold text-slate-900 dark:text-slate-100">{{ __('public.footer.legal') }}</p>
            <ul class="mt-3 space-y-2 text-slate-600 dark:text-slate-400">
                <li><a href="{{ route('public.legal', 'terms') }}">{{ __('public.nav.terms') }}</a></li>
                <li><a href="{{ route('public.legal', 'privacy') }}">{{ __('public.nav.privacy') }}</a></li>
                <li><a href="{{ route('public.legal', 'cookie') }}">{{ __('public.nav.cookie') }}</a></li>
                <li><a href="{{ route('public.legal', 'dpa') }}">{{ __('public.nav.dpa') }}</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-slate-200 py-4 text-center text-xs text-slate-500 dark:border-slate-800 dark:text-slate-500">
        © {{ date('Y') }} {{ config('public.brand.name') }}. {{ __('public.footer.rights') }}
    </div>
</footer>
