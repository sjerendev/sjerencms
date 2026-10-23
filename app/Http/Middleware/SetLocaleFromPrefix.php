<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromPrefix
{
    /**
     * URL prefix => app locale and Page::$language. The prefix is "se" like
     * the old site, the language code is "sv".
     */
    public const LOCALES = ['se' => 'sv', 'en' => 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $prefix = $request->route('locale');

        app()->setLocale(self::LOCALES[$prefix]);
        URL::defaults(['locale' => $prefix]);

        return $next($request);
    }
}
