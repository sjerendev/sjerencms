<?php

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\SitePagesSeeder;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(fn () => $this->actingAs(User::factory()->admin()->create()));

it('saves every seeded page untouched', function () {
    Storage::fake('public');
    $this->seed(SitePagesSeeder::class);

    expect(Page::count())->toBe(6);
    foreach (Page::all() as $page) {
        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])->call('save')->assertHasNoFormErrors();
    }
});

it('drops a pick whose project was deleted when the page is saved', function () {
    [$a, $b] = Project::factory()->count(2)->create();
    $picks = [['project_id' => $a->id], ['project_id' => $b->id]];
    $page = Page::create(['title' => 'Start', 'slug' => 'start', 'language' => 'sv', 'content' => [
        ['type' => 'featured_cases', 'data' => ['number' => '01', 'label' => 'Utvalda', 'aside' => null, 'projects' => $picks]],
        ['type' => 'register', 'data' => ['number' => '02', 'label' => 'Register', 'aside' => null, 'projects' => $picks, 'all_label' => null]],
    ]]);
    $b->delete();

    Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])->call('save')->assertHasNoFormErrors();

    foreach ($page->fresh()->content as $block) {
        expect(array_column(array_values($block['data']['projects']), 'project_id'))->toBe([$a->id]);
    }
});

it('rejects a second page with the same slug in the same language', function () {
    Page::create(['title' => 'Om mig', 'slug' => 'om-mig', 'language' => 'sv']);

    Livewire::test(CreatePage::class)
        ->fillForm(['title' => 'Om mig', 'slug' => 'om-mig', 'language' => 'sv'])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});

it('keeps the slug when the title changes on edit', function () {
    $page = Page::create(['title' => 'Om mig', 'slug' => 'om-mig', 'language' => 'en']);

    Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
        ->set('data.title', 'About')
        ->call('save')
        ->assertHasNoFormErrors();

    expect($page->fresh())->title->toBe('About')->slug->toBe('om-mig');
});
