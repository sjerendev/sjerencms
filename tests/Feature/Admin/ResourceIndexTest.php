<?php

use App\Models\User;

it('renders every resource index', function (string $url) {
    $this->actingAs(User::factory()->create())->get($url)->assertOk();
})->with([
    '/admin/pages',
    '/admin/posts',
    '/admin/categories',
    '/admin/products',
    '/admin/product-categories',
    '/admin/manage-settings',
]);
