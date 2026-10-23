<?php

use App\Models\User;

it('redirects guests to the admin login', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('lets an admin in', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin')->assertOk();
});

it('refuses a buyer at /admin', function () {
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
});
