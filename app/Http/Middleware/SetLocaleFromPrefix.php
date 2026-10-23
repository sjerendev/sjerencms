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

    /** The URL prefix for a language code: `sv` gives `se`. */
    public static function prefix(string $language): string
    {
        return array_flip(self::LOCALES)[$language];
    }

    /**
     * The hreflang map for a path under the locale prefix: language => URL.
     *
     * @return array<string, string>
     */
    public static function alternates(string $path): array
    {
        return collect(self::LOCALES)
            ->mapWithKeys(fn ($language, $prefix) => [$language => url("/{$prefix}/{$path}")])
            ->all();
    }
}
