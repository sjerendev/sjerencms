<?php

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\SitePagesSeeder;
use Filament\Actions\DeleteAction;
use Illuminate\Http\UploadedFile;
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

    // Each language gets its own CV on the upload disk, under the name the old site served it as.
    foreach (['sv' => 'Sjeren-Portfolio-cv-2024.pdf', 'en' => 'Sjeren-Portfolio-cv-en-2024.pdf'] as $language => $name) {
        $hero = collect(Page::where('language', $language)->where('slug', 'start')->sole()->content)->firstWhere('type', 'hero')['data'];
        expect($hero['cv_file_name'])->toBe($name)->and($hero)->not->toHaveKey('cv_url');
        Storage::disk('public')->assertExists($hero['cv_file']);
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

it('stores an uploaded CV under a random name and keeps the name it came with', function () {
    Storage::fake('public');
    Project::factory()->count(2)->create();
    $page = blockPage('hero', collect(everyBlock())->firstWhere('type', 'hero')['data']);
    $form = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]);
    $path = 'data.content.'.array_key_first($form->get('data.content')).'.data';

    $form->set("{$path}.cv_file", UploadedFile::fake()->create('Sjeren-CV.pdf', 100, 'application/pdf'))
        ->call('save')->assertHasNoFormErrors();

    $data = $page->fresh()->content[0]['data'];
    expect($data['cv_file'])->toStartWith('cv/')->not->toBe('cv/Sjeren-CV.pdf')
        ->and($data['cv_file_name'])->toBe('Sjeren-CV.pdf');
    Storage::disk('public')->assertExists($data['cv_file']);
});

it('deletes the replaced CV from the disk when a new one is saved', function () {
    Storage::fake('public');
    Storage::disk('public')->put('cv/a.pdf', '');
    Project::factory()->count(2)->create();
    $page = blockPage('hero', collect(everyBlock())->firstWhere('type', 'hero')['data']);
    $form = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]);
    $path = 'data.content.'.array_key_first($form->get('data.content')).'.data';

    // Emptied first, as the field does on a new pick: Livewire appends an upload to a filled field.
    $form->set("{$path}.cv_file", [])
        ->set("{$path}.cv_file", UploadedFile::fake()->create('Ny-CV.pdf', 100, 'application/pdf'))
        ->call('save')->assertHasNoFormErrors();

    Storage::disk('public')->assertMissing('cv/a.pdf');
    Storage::disk('public')->assertExists($page->fresh()->content[0]['data']['cv_file']);
});

it('keeps a replaced CV that another page still links to', function () {
    Storage::fake('public');
    Storage::disk('public')->put('cv/a.pdf', '');
    Project::factory()->count(2)->create();
    $hero = collect(everyBlock())->firstWhere('type', 'hero')['data'];
    $page = blockPage('hero', $hero);
    blockPage('hero', $hero, 'en');
    $form = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]);
    $path = 'data.content.'.array_key_first($form->get('data.content')).'.data';

    // Emptied first, as the field does on a new pick: Livewire appends an upload to a filled field.
    $form->set("{$path}.cv_file", [])
        ->set("{$path}.cv_file", UploadedFile::fake()->create('Ny-CV.pdf', 100, 'application/pdf'))
        ->call('save')->assertHasNoFormErrors();

    Storage::disk('public')->assertExists('cv/a.pdf');
});

it('deletes the CV from the disk when its page is deleted', function () {
    Storage::fake('public');
    Storage::disk('public')->put('cv/a.pdf', '');
    Project::factory()->count(2)->create();
    $page = blockPage('hero', collect(everyBlock())->firstWhere('type', 'hero')['data']);

    Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])->callAction(DeleteAction::class);

    expect(Page::count())->toBe(0);
    Storage::disk('public')->assertMissing('cv/a.pdf');
});

it('keeps the CV of a deleted page that another page still links to', function () {
    Storage::fake('public');
    Storage::disk('public')->put('cv/a.pdf', '');
    Project::factory()->count(2)->create();
    $hero = collect(everyBlock())->firstWhere('type', 'hero')['data'];
    $page = blockPage('hero', $hero);
    blockPage('hero', $hero, 'en');

    Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])->callAction(DeleteAction::class);

    Storage::disk('public')->assertExists('cv/a.pdf');
});

it('needs an uploaded CV and has no path field', function () {
    Project::factory()->count(2)->create();
    $page = blockPage('hero', ['cv_file' => null] + collect(everyBlock())->firstWhere('type', 'hero')['data']);
    $form = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]);
    $uuid = array_key_first($form->get('data.content'));

    $form->assertFormFieldDoesNotExist("content.{$uuid}.data.cv_url")
        ->call('save')->assertHasFormErrors(["content.{$uuid}.data.cv_file" => 'required']);
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
