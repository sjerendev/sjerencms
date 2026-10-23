<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Release;
use App\Models\Track;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('music');

    $this->buyer = User::factory()->create();
    $this->release = Release::factory()
        ->has(Track::factory()->withWav()->state(['position' => 1, 'title' => 'Deep Down']))
        ->create(['artist' => 'S.D.L.', 'title' => 'Low End']);
    $this->artisan('music:build-zips')->assertSuccessful();

    $this->order = Order::factory()->paid()->create(['user_id' => $this->buyer->id]);
    $this->trackItem = OrderItem::factory()->for($this->order)->create([
        'purchasable_type' => Track::class,
        'purchasable_id' => $this->release->tracks->first()->id,
        'title' => 'Deep Down',
    ]);
    $this->releaseItem = OrderItem::factory()->for($this->order)->create([
        'purchasable_type' => Release::class,
        'purchasable_id' => $this->release->id,
        'title' => 'Low End',
    ]);
});

it('streams the WAV to its owner and counts it', function () {
    $this->actingAs($this->buyer)->get(route('download', $this->trackItem))->assertDownload('S.D.L. - Deep Down.wav');

    expect($this->trackItem->fresh()->download_count)->toBe(1);
});

it('streams the release zip', function () {
    $this->actingAs($this->buyer)->get(route('download', $this->releaseItem))->assertDownload('S.D.L. - Low End.zip');

    expect($this->releaseItem->fresh()->download_count)->toBe(1);
});

it('lets the browser that paid download without logging in', function () {
    $this->withSession(['orders' => [$this->order->id]])->get(route('download', $this->trackItem))->assertDownload();
    $this->get(route('download', $this->trackItem))->assertDownload();
});

it('refuses strangers, unpaid and refunded orders', function () {
    $this->actingAs(User::factory()->create())->get(route('download', $this->trackItem))->assertForbidden();
    auth()->logout();
    $this->get(route('download', $this->trackItem))->assertForbidden();

    foreach ([OrderStatus::Unpaid, OrderStatus::Refunded] as $status) {
        $this->order->update(['status' => $status]);
        $this->actingAs($this->buyer)->get(route('download', $this->trackItem))->assertForbidden();
    }

    expect($this->trackItem->fresh()->download_count)->toBe(0);
});

it('does not let a guest match an order that has no owner', function () {
    $this->order->update(['user_id' => null]);

    $this->get(route('download', $this->trackItem))->assertForbidden();
});

it('stops at the limit', function () {
    $this->trackItem->update(['download_count' => 5]);

    $this->actingAs($this->buyer)->get(route('download', $this->trackItem))->assertForbidden();

    expect($this->trackItem->fresh()->download_count)->toBe(5)
        ->and($this->trackItem->fresh()->downloadsLeft())->toBe(0);
});

it('answers 404 and counts nothing while the zip is missing', function () {
    $this->release->getFirstMedia('zip')->delete();

    $this->actingAs($this->buyer)->get(route('download', $this->releaseItem))->assertNotFound();

    expect($this->releaseItem->fresh()->download_count)->toBe(0);
});

it('answers 404 and counts nothing when the file is gone from the disk', function () {
    $wav = $this->release->tracks->first()->getFirstMedia('wav');
    Storage::disk('music')->delete($wav->getPathRelativeToRoot());

    $this->actingAs($this->buyer)->get(route('download', $this->trackItem))->assertNotFound();

    expect($this->trackItem->fresh()->download_count)->toBe(0);
});
