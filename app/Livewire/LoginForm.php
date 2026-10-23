<?php

namespace App\Livewire;

use App\Mail\LoginLink;
use App\Models\User;
use App\Settings\GeneralSettings;
use App\Shop\LoginLinks;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * LoggaIn.dc.html. send() answers the same for a known buyer, an unknown
 * address, an admin (decision 8) and a rate-limited address, so the form
 * reveals nothing about who has an account.
 */
class LoginForm extends Component
{
    public string $email = '';

    public ?string $sentTo = null;

    public function send(): void
    {
        $this->email = Str::lower(trim($this->email));

        $invalid = __('shop.emailInvalid');
        $this->validate(
            ['email' => 'required|email|max:255'],
            ['email.required' => $invalid, 'email.email' => $invalid, 'email.max' => $invalid],
        );

        RateLimiter::attempt('login-link:'.sha1($this->email), 3, function () {
            // Case-insensitive on every database; SQLite and MySQL differ.
            $user = User::whereRaw('lower(email) = ?', [$this->email])->first();

            if ($user && ! $user->is_admin) {
                Mail::to($user->email)->locale(app()->getLocale())
                    ->send(new LoginLink(app(LoginLinks::class)->url($user)));
            }
        }, 600);

        $this->sentTo = $this->email;
    }

    public function render(): View
    {
        return view('livewire.login-form', ['minutes' => app(GeneralSettings::class)->login_link_minutes]);
    }
}
