<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocaleFromPrefix;
use App\Models\Release;
use Illuminate\View\View;

class ShowRelease
{
    public function __invoke(string $locale, Release $release): View
    {
        abort_unless($release->is_published, 404);

        $release->load('tracks');

        return view('music.show', [
            'release' => $release,
            'more' => Release::published()->newest()
                ->where('artist', $release->artist)
                ->whereKeyNot($release->getKey())
                ->limit(4)
                ->get(),
            'total' => Release::published()->count(),
            'title' => "{$release->title} — Patrik Sjerén",
            'description' => $release->description ?: __('meta.description'),
            'alternates' => collect(SetLocaleFromPrefix::LOCALES)
                ->mapWithKeys(fn ($language, $prefix) => [$language => url("/{$prefix}/musik/{$release->slug}")])
                ->all(),
        ]);
    }
}
