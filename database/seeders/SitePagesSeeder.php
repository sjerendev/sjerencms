<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * The home page, /om-mig and /projekt in both languages, so the site has
 * something to navigate between. Run on its own, it only adds pages that are
 * missing and never overwrites edits made in the admin:
 *
 *     php artisan db:seed --class=SitePagesSeeder
 *
 * All text is copied from sjeren-2026 messages/*.json.
 */
class SitePagesSeeder extends Seeder
{
    private const TOOLS = 'Figma · Penpot · Affinity · Adobe CC · Next.js · React · Nuxt · Vue.js · Laravel · Livewire · Filament · Astro · Node · Tailwind · GSAP · Docker · Forge · Envoyer · Vercel · Netlify · Webflow · Wordpress';

    public function run(): void
    {
        $pages = [
            'sv' => [
                'start' => ['title' => 'Patrik Sjerén', 'is_homepage' => true, 'content' => [$this->colophon('Verktyg')]],
                'om-mig' => [
                    'title' => 'Om mig',
                    'meta_title' => 'Om mig — Patrik Sjerén',
                    'meta_description' => 'Trettiosju år, två spår. Frontendutvecklare och designer i Malmö, med rötter i klubbmusiken.',
                ],
                'projekt' => [
                    'title' => 'Projekt',
                    'meta_title' => 'Projekt — Patrik Sjerén',
                    'meta_description' => 'Ett urval av mina arbeten. Design och kod för kunder i Skåne och resten av landet.',
                ],
            ],
            'en' => [
                'start' => ['title' => 'Patrik Sjerén', 'is_homepage' => true, 'content' => [$this->colophon('Tools')]],
                'om-mig' => [
                    'title' => 'About',
                    'meta_title' => 'About — Patrik Sjerén',
                    'meta_description' => 'Thirty-seven years, two tracks. Frontend developer and designer in Malmö, with roots in club music.',
                ],
                'projekt' => [
                    'title' => 'Projects',
                    'meta_title' => 'Projects — Patrik Sjerén',
                    'meta_description' => 'A selection of my work. Design and code for clients in Skåne and across Sweden.',
                ],
            ],
        ];

        foreach ($pages as $language => $bySlug) {
            foreach ($bySlug as $slug => $attributes) {
                Page::firstOrCreate(
                    ['slug' => $slug, 'language' => $language],
                    $attributes + ['is_published' => true, 'is_homepage' => false, 'content' => []],
                );
            }
        }
    }

    private function colophon(string $label): array
    {
        return ['type' => 'colophon', 'data' => ['label' => $label, 'tools' => self::TOOLS]];
    }
}
