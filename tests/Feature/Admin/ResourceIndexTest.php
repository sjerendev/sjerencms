<?php

use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use App\Models\User;
use Livewire\Livewire;

it('renders every resource index', function (string $url) {
    $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
})->with([
    '/admin/pages',
    '/admin/releases',
    '/admin/projects',
    '/admin/orders',
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

it('shows an order with its items and offers no way to edit it', function () {
    $this->actingAs(User::factory()->admin()->create());
    $order = Order::factory()->paid()->create(['id' => 42]);
    OrderItem::factory()->for($order)->create(['title' => 'Big Dripper', 'amount' => 70, 'download_count' => 3]);

    $this->get("/admin/orders/{$order->id}")->assertOk()->assertSee('00042')->assertSee('Big Dripper')->assertSee('70 kr');
    $this->get("/admin/orders/{$order->id}/edit")->assertNotFound();
    $this->get('/admin/orders/create')->assertNotFound();
});
