<?php

namespace App\Support;

use App\Models\Page;

/**
 * Each block's motion settings for a page, by block index. Motion is set once,
 * on the Swedish page: an English page takes the block at the same place on
 * the published Swedish page with the same slug, if it is the same type.
 */
class PageMotion
{
    /** @return array<int, array> */
    public static function for(Page $page): array
    {
        $source = $page->language === 'sv' ? $page : Page::published()->swedish()
            ->when($page->is_homepage, fn ($query) => $query->where('is_homepage', true), fn ($query) => $query->where('slug', $page->slug))
            ->first();

        $swedish = array_values($source->content ?? []);

        return array_map(
            fn (int $index, array $block) => ($swedish[$index]['type'] ?? null) === $block['type']
                ? ($swedish[$index]['data']['motion'] ?? [])
                : [],
            array_keys(array_values($page->content ?? [])),
            array_values($page->content ?? []),
        );
    }
}
