<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Release;
use App\Models\Track;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('music');

    $this->buyer = User::factory()->create(['email' => 'buyer@example.se']);
    $this->release = Release::factory()
        ->has(Track::factory()->withWav()->state(['position' => 1, 'title' => 'Deep Down']))
        ->create(['artist' => 'S.D.L.', 'title' => 'Past Voyages']);
    $this->track = $this->release->tracks->first();

    $this->order = Order::factory()->paid()->create([
        'user_id' => $this->buyer->id,
        'total' => 100,
        'paid_at' => '2026-10-22 12:00:00',
        'receipt_url' => 'https://pay.stripe.com/receipts/test',
    ]);
    $this->item = OrderItem::factory()->for($this->order)->create([
        'purchasable_type' => Track::class,
        'purchasable_id' => $this->track->id,
        'title' => 'Deep Down',
    ]);
});

it('requires a login', fn () => $this->get('/se/mina-kop')->assertRedirect());

it('shows the buyer their own paid items and nobody elses', function () {
    $other = Order::factory()->paid()->create(['user_id' => User::factory()->create()->id]);
    OrderItem::factory()->for($other)->create(['title' => 'Someone Elses', 'purchasable_id' => $this->track->id]);

    $this->actingAs($this->buyer)->get('/se/mina-kop')->assertOk()
        ->assertSee('buyer@example.se')->assertSee('Nedladdningar')
        ->assertSee('WAV · 5 gånger per köp')
        ->assertSee('Deep Down')->assertSee('S.D.L.')
        ->assertSee('5 av 5 kvar')->assertSee(route('download', $this->item))
        ->assertDontSee('Someone Elses');
});

it('lists the newest purchase first', function () {
    $newer = Order::factory()->paid()->create(['user_id' => $this->buyer->id, 'paid_at' => '2026-10-23 12:00:00']);
    OrderItem::factory()->for($newer)->create(['purchasable_id' => $this->track->id, 'title' => 'Newer One']);

    $this->actingAs($this->buyer)->get('/se/mina-kop')->assertSeeInOrder(['Newer One', 'Deep Down']);
});

it('shows an exhausted item with a link to write', function () {
    $this->item->update(['download_count' => 5]);

    $this->actingAs($this->buyer)->get('/se/mina-kop')->assertOk()
        ->assertSee('Slut på nedladdningar')->assertSee('0 av 5 kvar')
        ->assertSee('href="mailto:patrik.sjeren@gmail.com"', false)
        ->assertDontSee(route('download', $this->item));
});

it('leaves out the write link when no email is set', function () {
    $settings = app(GeneralSettings::class);
    $settings->email = null;
    $settings->save();
    $this->item->update(['download_count' => 5]);

    $this->actingAs($this->buyer)->get('/se/mina-kop')->assertOk()
        ->assertSee('Slut på nedladdningar')->assertDontSee('mailto:buyer', false)
        ->assertDontSee('Skriv till mig');
});

it('shows a receipt row for each paid order', function () {
    OrderItem::factory()->for($this->order)->create(['purchasable_id' => $this->track->id, 'title' => 'Second Title']);

    $this->actingAs($this->buyer)->get('/se/mina-kop')->assertOk()
        ->assertSee('Kvitton')
        ->assertSee($this->order->number())
        ->assertSee('22 okt 2026')
        ->assertSee('Deep Down, Second Title')
        ->assertSee('100 kr')
        ->assertSee('href="https://pay.stripe.com/receipts/test"', false)
        ->assertSee('Kvitto ↗');
});

it('writes the date in the language of the page', function () {
    $this->actingAs($this->buyer)->get('/en/mina-kop')->assertOk()->assertSee('22 Oct 2026');
});

it('has no receipt link when Stripe gave none', function () {
    $this->order->update(['receipt_url' => null]);

    $this->actingAs($this->buyer)->get('/se/mina-kop')->assertOk()
        ->assertSee($this->order->number())->assertDontSee('Kvitto ↗');
});

it('hides refunded and unpaid orders', function () {
    $refunded = Order::factory()->paid()->create(['user_id' => $this->buyer->id, 'status' => OrderStatus::Refunded]);
    OrderItem::factory()->for($refunded)->create(['purchasable_id' => $this->track->id, 'title' => 'Refunded One']);
    $unpaid = Order::factory()->create(['user_id' => $this->buyer->id]);
    OrderItem::factory()->for($unpaid)->create(['purchasable_id' => $this->track->id, 'title' => 'Unpaid One']);

    $this->actingAs($this->buyer)->get('/se/mina-kop')->assertOk()
        ->assertDontSee('Refunded One')->assertDontSee('Unpaid One')
        ->assertDontSee($refunded->number())->assertDontSee($unpaid->number());
});

it('says so when nothing is bought', function () {
    $this->actingAs(User::factory()->create())->get('/se/mina-kop')->assertOk()
        ->assertSee(__('shop.nothing'))->assertDontSee('Kvitton');
});

it('renders an item whose track is gone from its snapshot', function () {
    OrderItem::factory()->for($this->order)->create([
        'purchasable_type' => Track::class,
        'purchasable_id' => 99999,
        'title' => 'Vanished',
    ]);

    $this->actingAs($this->buyer)->get('/se/mina-kop')->assertOk()->assertSee('Vanished');
});
