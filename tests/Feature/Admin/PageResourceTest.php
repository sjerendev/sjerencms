<?php

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Models\Page;
use App\Models\User;
use Livewire\Livewire;

it('saves a page with a headline_text block and reopens it', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Om mig',
            'slug' => 'om-mig',
            'language' => 'sv',
            'content' => [[
                'type' => 'headline_text',
                'data' => [
                    'number' => '04',
                    'label' => 'Om mig',
                    'headline' => 'Rubrik',
                    'text' => 'Brödtext',
                ],
            ]],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $page = Page::where('slug', 'om-mig')->sole();
    expect(array_values($page->content)[0]['data']['headline'])->toBe('Rubrik');

    $this->get("/admin/pages/{$page->id}/edit")->assertOk()->assertSee('Rubrik');
});
