<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * The home page, /om-mig and /projekt in both languages, so the site has
 * something to navigate between. Run on its own, it only adds pages that are
 * missing and never overwrites edits made in the admin:
 *
 *     php artisan db:seed --class=SitePagesSeeder
 *
 * All text is copied from sjeren-2026 messages/*.json, about.ts and
 * projects.ts, via the snapshot in data/portfolio/. A page gets its blocks
 * when it is missing, empty (`[]`, also when emptied on purpose in the
 * admin) or still holds the phase-2 colophon, never when it has been edited.
 *
 * Projects are seeded (ProjectSeeder) only into an empty table, so a project
 * or image deleted in the admin stays deleted. Filling a page that picks a
 * deleted project throws.
 */
class SitePagesSeeder extends Seeder
{
    private const TOOLS = 'Figma · Penpot · Affinity · Adobe CC · Next.js · React · Nuxt · Vue.js · Laravel · Livewire · Filament · Astro · Node · Tailwind · GSAP · Docker · Forge · Envoyer · Vercel · Netlify · Webflow · Wordpress';

    private const CV = ['sv' => '/files/Sjeren-Portfolio-cv-2024.pdf', 'en' => '/files/Sjeren-Portfolio-cv-en-2024.pdf'];

    /** Page language to the key the Next site's messages and data use. */
    private const NEXT_LOCALE = ['sv' => 'se', 'en' => 'en'];

    private array $messages = [];

    private array $about = [];

    private array $projects = [];

    public function run(): void
    {
        // Only into an empty table: ProjectSeeder would bring back deleted projects and removed images.
        if (Project::doesntExist()) {
            $this->call(ProjectSeeder::class);
        }

        $dir = database_path('seeders/data/portfolio');
        $this->about = json_decode(file_get_contents("{$dir}/about.json"), true);
        $this->projects = json_decode(file_get_contents("{$dir}/projects.json"), true);
        foreach (self::NEXT_LOCALE as $language => $locale) {
            $this->messages[$language] = json_decode(file_get_contents("{$dir}/messages-{$locale}.json"), true);
        }

        $pages = [
            'sv' => [
                'start' => ['title' => 'Patrik Sjerén', 'is_homepage' => true],
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
                'start' => ['title' => 'Patrik Sjerén', 'is_homepage' => true],
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
                // Built only for a page that gets filled, so a no-op run never resolves picks.
                $content = fn () => match ($slug) {
                    'start' => $this->start($language),
                    'om-mig' => $this->about($language),
                    'projekt' => $this->projects($language),
                };

                $page = Page::firstOrCreate(
                    ['slug' => $slug, 'language' => $language],
                    fn () => $attributes + ['is_published' => true, 'is_homepage' => false, 'content' => $content()],
                );

                // `==`, not `===`: the stored JSON may order its keys differently.
                if (! $page->wasRecentlyCreated && ($page->content == [] || $page->content == $this->phaseTwo($slug, $language))) {
                    $page->update(['content' => $content()]);
                }
            }
        }
    }

    /** What the earlier seeder wrote, which counts as untouched. */
    private function phaseTwo(string $slug, string $language): array
    {
        return $slug === 'start' ? [$this->colophon($language === 'sv' ? 'Verktyg' : 'Tools')] : [];
    }

    private function start(string $language): array
    {
        $slugs = array_column($this->projects['projects'], 'slug');
        $featured = $this->projects['featured'];
        $preview = array_slice(array_values(array_diff($slugs, $featured)), 0, 4);

        $services = fn (string $title, string $items) => [
            'title' => $this->t($language, 'Services', $title),
            'items' => $this->lines($language, 'Services', $items),
        ];
        $card = fn (string $name) => [
            'title' => $this->t($language, 'Home', "aboutCard{$name}Title"),
            'body' => $this->t($language, 'Home', "aboutCard{$name}Body"),
        ];

        return [
            $this->block('hero', [
                'lead' => $this->t($language, 'Home', 'heroLead'),
                'phrases' => $this->lines($language, 'Home', 'heroPhrases'),
                'intro_one' => $this->t($language, 'Home', 'introOne'),
                'intro_two' => $this->t($language, 'Home', 'introTwo'),
                'work_label' => $this->t($language, 'Home', 'linkWork'),
                'cv_label' => $this->t($language, 'Home', 'linkCv'),
                'cv_url' => self::CV[$language],
            ]),
            $this->block('featured_cases', [
                'number' => '01',
                'label' => $this->t($language, 'Home', 'featuredLabel'),
                'aside' => $this->t($language, 'Home', 'featuredCount'),
                'projects' => $this->picks($featured),
            ]),
            $this->block('register', [
                'number' => '02',
                'label' => $this->t($language, 'Home', 'registerLabel'),
                'aside' => $this->t($language, 'Home', 'registerCount'),
                'projects' => $this->picks($preview),
                'all_label' => $this->t($language, 'Home', 'registerAll'),
            ]),
            $this->block('service_cards', [
                'number' => '03',
                'label' => $this->t($language, 'Home', 'servicesLabel'),
                'columns' => [
                    $services('designTitle', 'designItems'),
                    $services('devTitle', 'devItems'),
                    $services('graphicTitle', 'graphicItems'),
                    $services('techTitle', 'techItems'),
                ],
            ]),
            $this->block('headline_text', [
                'number' => '04',
                'label' => $this->t($language, 'Home', 'aboutLabel'),
                'headline' => $this->t($language, 'Home', 'aboutHeadline'),
                'text' => $this->t($language, 'Home', 'aboutBody'),
                'cards' => [$card('Design'), $card('Dev'), $card('Free')],
            ]),
            $this->colophon($this->t($language, 'Colophon', 'label'), $this->t($language, 'Colophon', 'tools')),
        ];
    }

    private function about(string $language): array
    {
        $locale = self::NEXT_LOCALE[$language];
        $t = fn (string $key) => $this->t($language, 'About', $key);

        return [
            $this->block('page_hero', [
                'lines' => $t('heroOne')."\n".$t('heroTwo'),
                'text' => $t('heroBody'),
                'size' => 'large',
            ]),
            $this->block('profile', [
                'number' => '01',
                'label' => $t('profileLabel'),
                'column_one' => $t('profileOne')."\n\n".$t('profileTwo'),
                'column_two' => $t('profileThree')."\n\n".$t('profileFour'),
                'stats' => [
                    ['value' => '25', 'label' => $t('statYears')],
                    ['value' => '15', 'label' => $t('statAgency')],
                    ['value' => '1989', 'label' => $t('statSound')],
                ],
            ]),
            $this->block('timeline', [
                'number' => '02',
                'label' => $t('experienceLabel'),
                'work_label' => $t('trackWork'),
                'sound_label' => $t('trackSound'),
                'entries' => array_map(fn (array $e) => [
                    'years' => $e['years'][$locale],
                    'title' => $e['title'],
                    'subtitle' => $e['subtitle'][$locale],
                    'body' => $e['body'][$locale],
                    'tags' => $e['tags'] ?? [],
                    'track' => $e['track'],
                ], $this->about['timeline']),
            ]),
            $this->block('stack', [
                'number' => '03',
                'label' => $t('stackLabel'),
                'groups' => array_map(fn (array $g) => ['label' => $g['label'][$locale], 'items' => $g['items']], $this->about['stack']),
            ]),
            $this->block('faq_accordion', [
                'number' => '04',
                'label' => $t('questionsLabel'),
                'items' => array_map(fn (array $q) => [
                    'question' => $q['q'][$locale],
                    'answer' => $q['a'][$locale],
                    'note' => $q['note'][$locale],
                ], $this->about['questions']),
            ]),
        ];
    }

    private function projects(string $language): array
    {
        return [
            $this->block('page_hero', [
                'lines' => $this->t($language, 'Projects', 'heroCount'),
                'text' => $this->t($language, 'Projects', 'heroBody'),
                'size' => 'medium',
            ]),
            // No projects: the register shows every project, by position.
            $this->block('register', [
                'number' => '01',
                'label' => $this->t($language, 'Projects', 'listLabel'),
                'aside' => '{total}',
                'projects' => [],
            ]),
        ];
    }

    private function block(string $type, array $data): array
    {
        return ['type' => $type, 'data' => $data];
    }

    private function colophon(string $label, string $tools = self::TOOLS): array
    {
        return $this->block('colophon', ['label' => $label, 'tools' => $tools]);
    }

    /** UI text from the Next site's messages. A missing key is an error: copy is never invented. */
    private function t(string $language, string $section, string $key): string
    {
        return $this->messages[$language][$section][$key]
            ?? throw new \RuntimeException("Missing message {$section}.{$key} ({$language})");
    }

    /** A `|` separated message as one item per line. */
    private function lines(string $language, string $section, string $key): string
    {
        return str_replace('|', "\n", $this->t($language, $section, $key));
    }

    /** Slugs to the picker's `[['project_id' => id], …]`, in the given order. An unknown slug is an error. */
    private function picks(array $slugs): array
    {
        $ids = Project::whereIn('slug', $slugs)->pluck('id', 'slug');

        return array_map(
            fn (string $slug) => ['project_id' => $ids[$slug] ?? throw new \RuntimeException("Missing project {$slug}")],
            $slugs,
        );
    }
}
