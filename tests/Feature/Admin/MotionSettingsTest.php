<?php

use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
    // The hero's CV is required, and the upload field drops a file that isn't on the disk.
    Storage::disk('public')->put('cv/a.pdf', '');
    Project::factory()->count(2)->create();
    $this->actingAs(User::factory()->admin()->create());
});

it('keeps motion settings when the page is saved', function (string $type, string $key) {
    $block = collect(everyBlock())->firstWhere('type', $type);
    $block['data']['motion'] = [$key => ['enabled' => true, 'from' => 'left', 'split' => 'words']];
    $page = Page::create(['title' => 'M', 'slug' => 'm', 'language' => 'sv', 'content' => [$block]]);

    $form = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]);
    $uuid = array_key_first($form->get('data.content'));

    // Filament keeps unknown block data on save, so the saved value alone would not prove the panel exists.
    $form->assertFormFieldExists("content.{$uuid}.data.motion.{$key}.enabled")
        ->call('save')->assertHasNoFormErrors();

    expect(collect($page->fresh()->content)->first()['data']['motion'][$key])
        ->toMatchArray(['enabled' => true, 'from' => 'left', 'split' => 'words']);
})->with([
    ['hero', 'lead'], ['hero', 'intro_one'], ['hero', 'intro_two'],
    ['page_hero', 'lines'], ['page_hero', 'text'],
    ['headline_text', 'head'], ['headline_text', 'headline'], ['headline_text', 'text'],
    ['headline_text', 'cards_title'], ['headline_text', 'cards_body'],
    ['service_cards', 'head'], ['service_cards', 'columns_title'], ['service_cards', 'columns_items'],
    ['faq_accordion', 'head'], ['faq_accordion', 'items_question'], ['faq_accordion', 'items_answer'], ['faq_accordion', 'items_note'],
    ['profile', 'head'], ['profile', 'column_one'], ['profile', 'column_two'], ['profile', 'stats_value'], ['profile', 'stats_label'],
    ['timeline', 'head'], ['timeline', 'entries_years'], ['timeline', 'entries_title'], ['timeline', 'entries_subtitle'], ['timeline', 'entries_body'],
    ['stack', 'head'], ['stack', 'groups_label'], ['stack', 'groups_items'],
    ['featured_cases', 'head'], ['register', 'head'], ['colophon', 'label'],
]);

it('fills in the defaults when motion is turned on', function () {
    $page = blockPage('hero', collect(everyBlock())->firstWhere('type', 'hero')['data']);
    $form = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]);
    $path = 'data.content.'.array_key_first($form->get('data.content')).'.data.motion.intro_one';

    $form->set("{$path}.enabled", true)
        ->assertSet("{$path}.from", 'bottom')->assertSet("{$path}.duration", 0.8)->assertSet("{$path}.mask", false);
});

it('tells an English page that motion is set on the Swedish page', function () {
    $sv = blockPage('hero', collect(everyBlock())->firstWhere('type', 'hero')['data']);
    $en = blockPage('hero', collect(everyBlock())->firstWhere('type', 'hero')['data'], 'en');

    $this->get("/admin/pages/{$en->id}/edit")->assertSee('Motion is set on the Swedish page.');
    $this->get("/admin/pages/{$sv->id}/edit")->assertDontSee('Motion is set on the Swedish page.')
        ->assertSee('Motion: Intro one');
});
