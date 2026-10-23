<?php

namespace App\Shop;

use App\Http\Middleware\SetLocaleFromPrefix;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * One active link per buyer (decision 12): url() replaces the stored hash, so
 * the previous link stops working. Only the hash is stored.
 */
class LoginLinks
{
    public function url(User $user, ?string $language = null): string
    {
        $nonce = Str::random(40);
        $user->forceFill(['login_nonce' => hash('sha256', $nonce)])->save();

        return URL::temporarySignedRoute(
            'shop.login.confirm',
            now()->addMinutes(app(GeneralSettings::class)->login_link_minutes),
            [
                'locale' => SetLocaleFromPrefix::prefix($language ?? app()->getLocale()),
                'user' => $user->id,
                'nonce' => $nonce,
            ],
        );
    }

    /** Read-only, so the confirm page can tell a spent link from a live one on GET. */
    public function valid(User $user, string $nonce): bool
    {
        return hash_equals((string) $user->login_nonce, hash('sha256', $nonce));
    }

    /** Atomic, so two requests with the same link cannot both log in. */
    public function consume(User $user, string $nonce): bool
    {
        return User::whereKey($user->id)->where('login_nonce', hash('sha256', $nonce))
            ->update(['login_nonce' => null]) === 1;
    }
}
