<?php

use App\Models\User;

it('redirects guests to the admin login', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('shows the dashboard to a logged in user', function () {
    $this->actingAs(User::factory()->create())->get('/admin')->assertOk();
});
