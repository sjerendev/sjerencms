<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocaleFromPrefix;
use App\Models\Release;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowMusic
{
    public function __invoke(Request $request, string $locale): View
    {
        $aliases = Release::aliases();
        // An unknown or outdated alias shows everything instead of an empty page.
        $active = $aliases->firstWhere('slug', $request->query('alias'));

        $releases = Release::published()->newest()->with('media', 'tracks.media')
            ->when($active, fn ($query) => $query->where('artist', $active['name']))
            ->get();

        return view('music.index', [
            'aliases' => $aliases,
            'active' => $active['slug'] ?? null,
            'total' => $aliases->sum('count'),
            'releases' => $releases,
            'title' => __('music.title'),
            'description' => __('meta.description'),
            'alternates' => SetLocaleFromPrefix::alternates('musik'),
        ]);
    }
}
