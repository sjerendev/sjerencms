<?php

use App\Filament\Pages\ManageSettings;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

it('saves general settings', function () {
    $this->actingAs(User::factory()->create());

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
