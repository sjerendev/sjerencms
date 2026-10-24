<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocaleFromPrefix;
use App\Models\Page;
use App\Support\PageMotion;
use Illuminate\View\View;

class ShowPage
{
    public function __invoke(string $locale, ?string $slug = null): View
    {
        $page = $this->find(SetLocaleFromPrefix::LOCALES[$locale], $slug) ?? abort(404);

        $alternates = [];
        foreach (SetLocaleFromPrefix::LOCALES as $prefix => $language) {
            if ($this->find($language, $slug)) {
                $alternates[$language] = url($slug ? "/{$prefix}/{$slug}" : "/{$prefix}");
            }
        }

        return view('pages.show', [
            'page' => $page,
            'title' => $page->meta_title ?: __('meta.title'),
            'description' => $page->meta_description ?: __('meta.description'),
            'alternates' => $alternates,
            'motions' => PageMotion::for($page),
        ]);
    }

    private function find(string $language, ?string $slug): ?Page
    {
        return Page::published()->language($language)
            ->when($slug, fn ($query) => $query->where('slug', $slug), fn ($query) => $query->where('is_homepage', true))
            ->first();
    }
}
