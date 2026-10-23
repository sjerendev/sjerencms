<?php

use App\Livewire\LoginForm;
use App\Mail\LoginLink;
use App\Models\Track;
use App\Models\User;
use App\Shop\Cart;
use App\Shop\LoginLinks;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

beforeEach(function () {
    Mail::fake();
    // Livewire::test skips the HTTP middleware that sets these.
    app()->setLocale('sv');
    URL::defaults(['locale' => 'se']);
});

it('mails a login link to a known buyer', function () {
    User::factory()->create(['email' => 'name@example.se']);

    Livewire::test(LoginForm::class)->set('email', ' Name@Example.se ')->call('send')
        ->assertSee('Länken är skickad till name@example.se')
        ->assertSee('Den gäller i 15 minuter');

    Mail::assertSent(LoginLink::class, fn ($mail) => $mail->hasTo('name@example.se'));
});

it('answers the same and sends nothing for an unknown address or an admin', function ($email) {
    Livewire::test(LoginForm::class)->set('email', value($email))->call('send')
        ->assertSee('Länken är skickad till');

    Mail::assertNothingSent();
})->with([
    'unknown' => 'nobody@example.se',
    'admin' => fn () => User::factory()->admin()->create()->email,
]);

it('asks for a valid address', function () {
    Livewire::test(LoginForm::class)->set('email', 'not-an-address')->call('send')
        ->assertSee('Ange en giltig e-postadress')->assertDontSee('Länken är skickad');

    Mail::assertNothingSent();
});

it('sends at most three links per address in ten minutes', function () {
    User::factory()->create(['email' => 'name@example.se']);
    $form = Livewire::test(LoginForm::class);

    foreach (range(1, 4) as $_) {
        $form->set('email', 'name@example.se')->call('send')->assertSee('Länken är skickad');
    }

    Mail::assertSentCount(3);
});

it('renders the mail with the link', function () {
    $url = 'https://sjerencms.test/se/logga-in/1?signature=abc';

    expect((new LoginLink($url))->render())->toContain(e($url));
});

it('does not log in or burn the link on GET', function () {
    $buyer = User::factory()->create();
    $url = app(LoginLinks::class)->url($buyer);

    $this->get($url)->assertOk()->assertSee('Logga in')->assertSee('method="post"', false)->assertSee('name="_token"', false);
    $this->assertGuest();
    expect($buyer->fresh()->login_nonce)->not->toBeNull();
});

it('logs in once on POST', function () {
    $buyer = User::factory()->create();
    $url = app(LoginLinks::class)->url($buyer);

    $this->post($url)->assertRedirect('/se/mina-kop');
    $this->assertAuthenticatedAs($buyer);

    auth()->logout();
    $this->post($url)->assertRedirect('/se/logga-in');
    $this->get($url)->assertRedirect('/se/logga-in');
    $this->assertGuest();
});

it('builds the link in the language of the page', function () {
    $buyer = User::factory()->create();

    expect(app(LoginLinks::class)->url($buyer, 'en'))->toStartWith(url('/en/logga-in/'.$buyer->id));
});

it('rejects an expired, tampered or replaced link', function (string $spoil) {
    $buyer = User::factory()->create();
    $url = app(LoginLinks::class)->url($buyer);

    match ($spoil) {
        'expired' => $this->travel(16)->minutes(),
        'other nonce' => $url = preg_replace('/nonce=\w{40}/', 'nonce='.str_repeat('a', 40), $url),
        'other user' => $url = str_replace('/logga-in/'.$buyer->id, '/logga-in/'.($buyer->id + 1), $url),
        'replaced' => app(LoginLinks::class)->url($buyer),
    };

    $this->post($url)->assertRedirect('/se/logga-in');
    $this->assertGuest();
    $this->get($url)->assertRedirect('/se/logga-in')->assertSessionHas('expired', '[Länken gäller inte längre. Skicka en ny.]');
})->with(['expired', 'other nonce', 'other user', 'replaced']);

it('does not let a link log in an admin', function () {
    $admin = User::factory()->admin()->create();
    $url = app(LoginLinks::class)->url($admin);

    $this->post($url)->assertRedirect('/se/logga-in');
    $this->assertGuest();
});

it('sends guests from Mina köp to the login page', function () {
    $this->get('/se/mina-kop')->assertRedirect('/se/logga-in');
    $this->get('/en/mina-kop')->assertRedirect('/en/logga-in');
});

it('shows the account header to a buyer', function () {
    $buyer = User::factory()->create(['email' => 'name@example.se']);

    $this->actingAs($buyer)->get('/se/mina-kop')->assertOk()
        ->assertSee('Mina köp')->assertSee('name@example.se')->assertSee('Logga ut')
        ->assertSee('aria-current="page"', false)->assertDontSee('Logga in');
});

it('offers the login link in the header and the checkout', function () {
    $this->get('/se/logga-in')->assertOk()->assertSee('Skicka inloggningslänk')
        ->assertSee('href="'.url('/se/logga-in').'"', false);
});

it('logs out', function () {
    $this->actingAs(User::factory()->create())->post('/se/logga-ut')->assertRedirect('/se/logga-in');
    $this->assertGuest();
});

it('links the checkout to the login page', function () {
    $track = Track::factory()->withWav()->create();
    app(Cart::class)->add('track', $track->id, 35);

    $this->get('/se/kassa')->assertSee('Har du handlat förut? Logga in')
        ->assertSee('href="'.url('/se/logga-in').'"', false);
});

it('treats a new user as not an admin', function () {
    expect((new User)->is_admin)->toBeFalse()->and((new User)->canAccessPanel(Filament\Facades\Filament::getPanel('admin')))->toBeFalse();
});
