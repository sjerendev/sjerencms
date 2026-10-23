<?php

use App\Models\Page;
use Database\Seeders\SitePagesSeeder;

it('seeds the six site pages idempotently', function () {
    $this->seed(SitePagesSeeder::class);
    $this->seed(SitePagesSeeder::class);

    expect(Page::count())->toBe(6);
    foreach (['/se', '/en', '/se/om-mig', '/en/om-mig', '/se/projekt', '/en/projekt'] as $url) {
        $this->get($url)->assertOk();
    }
    $this->get('/en/om-mig')->assertSee('<title>About — Patrik Sjerén</title>', false);
    $this->get('/se')->assertSee('data-motion="colophon"', false);
});
