<?php

use App\Filament\Pages\ManageSettings;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

it('saves general settings', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(ManageSettings::class)
        ->fillForm([
            'site_name' => 'Patrik Sjerén',
            'tagline' => 'Portfolio',
            'navigation_type' => 'normal',
            'youtube_url' => 'https://youtube.com/@x',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(GeneralSettings::class)->refresh();
    expect($settings->site_name)->toBe('Patrik Sjerén')
        ->and($settings->youtube_url)->toBe('https://youtube.com/@x');

    $stored = DB::table('settings')->where('group', 'general')->where('name', 'youtube_url')->value('payload');
    expect(json_decode($stored))->toBe('https://youtube.com/@x');
});

it('stores the logo on the public disk', function () {
    $this->actingAs(User::factory()->admin()->create());

    $logo = Livewire::test(ManageSettings::class)->instance()->form->getComponent('logo');

    expect($logo->getDiskName())->toBe('public');
});

it('has default shop limits', function () {
    $settings = app(GeneralSettings::class);

    expect($settings->download_limit)->toBe(5)
        ->and($settings->login_link_minutes)->toBe(15);
});

it('saves the shop limits', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(ManageSettings::class)
        ->fillForm(['download_limit' => 3, 'login_link_minutes' => 30])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(GeneralSettings::class)->refresh();
    expect($settings->download_limit)->toBe(3)
        ->and($settings->login_link_minutes)->toBe(30);
});
