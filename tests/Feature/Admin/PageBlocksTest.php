<?php

use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;
use Filament\Forms\Components\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
    foreach (['content-images/a.jpg', 'sticky-carousel/a.jpg', 'sticky-carousel/b.jpg', 'sticky-carousel/c.jpg', 'cv/a.pdf'] as $file) {
        Storage::disk('public')->put($file, 'x');
    }

    Project::factory()->count(2)->create();
    $this->actingAs(User::factory()->admin()->create());
    $this->page = Page::create(['title' => 'All', 'slug' => 'all', 'language' => 'sv', 'content' => everyBlock()]);
});

it('renders the edit page with every block', function () {
    $this->get("/admin/pages/{$this->page->id}/edit")->assertOk()
        ->assertSee(['Headline', 'Call to action', 'Six questions', 'Services', 'Stagger', 'Verktyg', 'Ibiza', 'Works with']);
});

it('saves the edit page with every block in order', function () {
    Livewire::test(EditPage::class, ['record' => $this->page->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(collect($this->page->fresh()->content)->pluck('type')->all())
        ->toBe(collect(everyBlock())->pluck('type')->all());
});

it('registers every block type in the page form', function () {
    // Saving keeps an unknown block's data, so only the form can tell a missing registration.
    $builder = Livewire::test(EditPage::class, ['record' => $this->page->getRouteKey()])
        ->instance()->getSchema('form')->getComponent(fn ($component) => $component instanceof Builder);

    foreach (collect(everyBlock())->pluck('type') as $type) {
        expect($builder->hasBlock($type))->toBeTrue("{$type} is not registered");
    }
});
