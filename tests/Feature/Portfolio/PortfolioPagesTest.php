<?php

use App\Models\Page;
use App\Models\Project;
use Database\Seeders\SitePagesSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

it('builds the start page in the order of the Next site', function () {
    $this->seed(SitePagesSeeder::class);

    expect(collect(Page::where('is_homepage', true)->where('language', 'sv')->first()->content)->pluck('type')->all())
        ->toBe(['hero', 'featured_cases', 'register', 'service_cards', 'headline_text', 'colophon']);
});

it('features the three cases and previews the next four', function () {
    $this->seed(SitePagesSeeder::class);
    $data = json_decode(file_get_contents(database_path('seeders/data/portfolio/projects.json')), true);
    $preview = array_slice(array_values(array_diff(array_column($data['projects'], 'slug'), $data['featured'])), 0, 4);
    $content = collect(Page::where('is_homepage', true)->where('language', 'sv')->first()->content)->keyBy('type');
    $slugs = fn (string $type) => Project::chosen(array_column($content[$type]['data']['projects'], 'project_id'))->pluck('slug')->all();

    expect($slugs('featured_cases'))->toBe($data['featured'])->and($slugs('register'))->toBe($preview);
});

it('serves the three pages in both languages with the live total', function () {
    $this->seed(SitePagesSeeder::class);
    $total = Project::count();

    $this->get('/se')->assertOk()->assertSee(['Jag levererar', "Tre av {$total}", "Visa alla {$total}"]);
    $this->get('/en/om-mig')->assertOk()->assertSee(['Last-minute trip, aged 19', 'Hosting & ops']);
    $this->get('/se/projekt')->assertOk()->assertSee('/ '.str_pad((string) $total, 3, '0', STR_PAD_LEFT));
});

it('fills a page that still has its phase-2 content', function () {
    Page::create(['title' => 'Patrik Sjerén', 'slug' => 'start', 'language' => 'sv', 'is_homepage' => true, 'is_published' => true,
        'content' => [['type' => 'colophon', 'data' => ['label' => 'Verktyg', 'tools' => 'Figma · Penpot · Affinity · Adobe CC · Next.js · React · Nuxt · Vue.js · Laravel · Livewire · Filament · Astro · Node · Tailwind · GSAP · Docker · Forge · Envoyer · Vercel · Netlify · Webflow · Wordpress']]]]);

    $this->seed(SitePagesSeeder::class);

    expect(count(Page::where('slug', 'start')->where('language', 'sv')->first()->content))->toBe(6);
});

it('leaves content edited in the admin alone', function () {
    $this->seed(SitePagesSeeder::class);
    $page = Page::where('slug', 'om-mig')->where('language', 'sv')->first();
    $page->update(['content' => [['type' => 'stack', 'data' => ['number' => '01', 'label' => 'Egen', 'groups' => []]]]]);

    $this->seed(SitePagesSeeder::class);

    expect($page->fresh()->content[0]['data']['label'])->toBe('Egen');
});
