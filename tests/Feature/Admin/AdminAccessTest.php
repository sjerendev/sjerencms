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

it('lets an admin change the password on the profile page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/admin/profile')->assertOk();

    Livewire\Livewire::test(Filament\Auth\Pages\EditProfile::class)
        ->fillForm([
            'password' => 'a-new-password-123',
            'passwordConfirmation' => 'a-new-password-123',
            'currentPassword' => 'password',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Illuminate\Support\Facades\Hash::check('a-new-password-123', $admin->fresh()->password))->toBeTrue();
});
