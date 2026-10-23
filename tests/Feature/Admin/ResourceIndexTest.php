<?php

use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use App\Models\User;
use Livewire\Livewire;

it('renders every resource index', function (string $url) {
    $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
})->with([
    '/admin/pages',
    '/admin/releases',
    '/admin/manage-settings',
]);

it('searches the page list', function () {
    $this->actingAs(User::factory()->admin()->create());
    Page::create(['title' => 'Om mig', 'slug' => 'om-mig', 'language' => 'sv']);
    Page::create(['title' => 'Kontakt', 'slug' => 'kontakt', 'language' => 'sv']);

    Livewire::test(ListPages::class)
        ->searchTable('Om')
        ->assertCanSeeTableRecords(Page::where('slug', 'om-mig')->get())
        ->assertCanNotSeeTableRecords(Page::where('slug', 'kontakt')->get());
});
