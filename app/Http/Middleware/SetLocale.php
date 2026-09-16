<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Every public URL carries its locale as the first segment, so a page and its
 * translation are distinct addresses that can be linked, shared and indexed.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $locale = $route?->parameter('locale');

        if (! array_key_exists($locale, config('site.locales'))) {
            $locale = config('site.default_locale');
        }

        app()->setLocale($locale);

        // Registering the locale as a URL default is what keeps every route()
        // call in the views free of a locale argument: they inherit the one
        // being served.
        URL::defaults(['locale' => $locale]);

        // Dropped from the route so it is not handed to controller actions as
        // their first argument, which would shift every model binding along by
        // one and break route model binding.
        $route?->forgetParameter('locale');

        return $next($request);
    }
}
