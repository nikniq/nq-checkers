<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Throwable;

class SocialAuthController extends Controller
{
    public const PROVIDERS = ['google'];

    public function redirect(string $provider): SymfonyRedirect
    {
        abort_unless(self::enabled($provider), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        abort_unless(self::enabled($provider), 404);

        try {
            $social = Socialite::driver($provider)->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors(['email' => 'Sign-in with '.ucfirst($provider).' failed. Please try again.']);
        }

        if (! $social->getEmail()) {
            return redirect()->route('login')->withErrors(['email' => ucfirst($provider).' did not share an email address.']);
        }

        $column = $provider.'_id';
        $user = User::where($column, $social->getId())->first()
            ?? User::where('email', $social->getEmail())->first();

        if ($user) {
            $user->forceFill([$column => $social->getId(), 'avatar' => $user->avatar ?: $social->getAvatar()]);
            $user->email_verified_at ??= now();
            $user->save();
        } else {
            $user = new User([
                'name' => mb_substr($social->getName() ?: $social->getNickname() ?: strtok($social->getEmail(), '@'), 0, 50),
                'email' => $social->getEmail(),
            ]);
            $user->forceFill([$column => $social->getId(), 'avatar' => $social->getAvatar(), 'email_verified_at' => now()])->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public static function enabled(string $provider): bool
    {
        return in_array($provider, self::PROVIDERS, true) && filled(config("services.$provider.client_id"));
    }
}
