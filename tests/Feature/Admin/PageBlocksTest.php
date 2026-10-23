<?php

use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function everyBlock(): array
{
    return [
        ['type' => 'image_text', 'data' => ['image' => 'content-images/a.jpg', 'text' => 'Image text', 'reverse_layout' => false]],
        ['type' => 'full_text', 'data' => ['text' => 'Full text']],
        ['type' => 'headline_text', 'data' => ['container_type' => 'container', 'headline' => 'Headline', 'text' => 'Body']],
        ['type' => 'sticky_carousel', 'data' => ['section_height' => '100vh', 'images' => [
            ['image' => 'sticky-carousel/a.jpg', 'title' => 'A', 'url' => 'https://a.se'],
            ['image' => 'sticky-carousel/b.jpg', 'title' => 'B', 'url' => 'https://b.se'],
            ['image' => 'sticky-carousel/c.jpg', 'title' => 'C', 'url' => 'https://c.se'],
        ]]],
        ['type' => 'call_to_action', 'data' => ['headline' => 'Call to action', 'text' => 'Text', 'button_text' => 'Go', 'button_url' => '/x', 'background_type' => 'color', 'background_color' => '#ffffff']],
        ['type' => 'faq_accordion', 'data' => ['heading' => 'Questions', 'items' => [['question' => 'Q', 'answer' => 'A']]]],
        ['type' => 'service_cards', 'data' => ['section_title' => 'Services', 'cards' => [['icon' => 'gear', 'icon_color' => '#000000', 'title' => 'T', 'description' => 'D']]]],
        ['type' => 'ticker', 'data' => ['background_color' => '#ffffff', 'text_color' => '#000000', 'angle' => 0, 'direction' => 'left-to-right', 'words' => ['a', 'b']]],
        ['type' => 'stagger_text', 'data' => ['text_row_1' => 'Stagger', 'color_row_1' => '#000000']],
    ];
}

beforeEach(function () {
    Storage::fake('public');
    foreach (['content-images/a.jpg', 'sticky-carousel/a.jpg', 'sticky-carousel/b.jpg', 'sticky-carousel/c.jpg'] as $file) {
        Storage::disk('public')->put($file, 'x');
    }

    $this->actingAs(User::factory()->create());
    $this->page = Page::create(['title' => 'All', 'slug' => 'all', 'language' => 'sv', 'content' => everyBlock()]);
});

it('renders the edit page with every block', function () {
    $this->get("/admin/pages/{$this->page->id}/edit")->assertOk()
        ->assertSee(['Headline', 'Call to action', 'Questions', 'Services', 'Stagger']);
});

it('saves the edit page with every block in order', function () {
    Livewire::test(EditPage::class, ['record' => $this->page->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(collect($this->page->fresh()->content)->pluck('type')->all())
        ->toBe(collect(everyBlock())->pluck('type')->all());
});
