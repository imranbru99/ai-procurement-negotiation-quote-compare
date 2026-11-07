<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetPublicLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locales = array_keys(config('public.locales', ['en' => []]));
        $locale = $request->session()->get('public_locale', config('public.default_locale', 'en'));

        if ($request->has('locale') && in_array($request->query('locale'), $locales, true)) {
            $locale = (string) $request->query('locale');
            $request->session()->put('public_locale', $locale);
        }

        if (! in_array($locale, $locales, true)) {
            $locale = config('public.default_locale', 'en');
        }

        App::setLocale($locale);

        $rtl = (bool) (config('public.locales.'.$locale.'.rtl') ?? false);
        View::share('publicLocale', $locale);
        View::share('publicRtl', $rtl);
        View::share('publicLocales', config('public.locales', []));

        return $next($request);
    }
}
