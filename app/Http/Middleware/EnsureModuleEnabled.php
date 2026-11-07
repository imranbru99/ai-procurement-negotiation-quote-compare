<?php

namespace App\Http\Middleware;

use App\Facades\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        if (! Module::enabled($moduleKey)) {
            abort(404);
        }

        return $next($request);
    }
}
