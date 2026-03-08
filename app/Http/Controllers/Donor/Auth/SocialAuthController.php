<?php

namespace App\Http\Controllers\Donor\Auth;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use App\Models\DonorSocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialAuthController extends Controller
{
    protected array $supportedProviders = ['google', 'facebook'];

    public function redirect(string $provider, Request $request): RedirectResponse
    {
        abort_unless(in_array($provider, $this->supportedProviders, true), 404);

        $locale = app()->getLocale();

        session([
            'social_auth.locale' => $locale,
        ]);

        $driver = Socialite::driver($provider);

        if ($provider === 'google') {
            $driver = $driver->scopes(['openid', 'profile', 'email']);
        }

        if ($provider === 'facebook') {
            $driver = $driver->scopes(['email']);
        }

        return $driver->redirect();
    }

    public function callback(string $provider, Request $request)
    {
        abort_unless(in_array($provider, $this->supportedProviders, true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            $locale = session('social_auth.locale', 'ar');

            return redirect()->to($locale === 'en' ? '/en/login' : '/login')
                ->withErrors([
                    'social' => $locale === 'en'
                        ? 'Unable to complete social login. Please try again.'
                        : 'تعذر إتمام تسجيل الدخول الاجتماعي. يرجى المحاولة مرة أخرى.',
                ]);
        }

        $email = $socialUser->getEmail();
        $providerUserId = (string) $socialUser->getId();

        if (empty($providerUserId)) {
            $locale = session('social_auth.locale', 'ar');

            return redirect()->to($locale === 'en' ? '/en/login' : '/login')
                ->withErrors([
                    'social' => $locale === 'en'
                        ? 'Invalid account data returned from provider.'
                        : 'تم إرجاع بيانات غير صالحة من مزود تسجيل الدخول.',
                ]);
        }

        if ($provider === 'facebook' && empty($email)) {
            $locale = session('social_auth.locale', 'ar');

            return redirect()->to($locale === 'en' ? '/en/login' : '/login')
                ->withErrors([
                    'social' => $locale === 'en'
                        ? 'Facebook did not return an email address for this account.'
                        : 'فيسبوك لم يرسل بريدًا إلكترونيًا لهذا الحساب.',
                ]);
        }

        $donor = DB::transaction(function () use ($provider, $providerUserId, $socialUser, $email) {
            $socialAccount = DonorSocialAccount::query()
                ->where('provider', $provider)
                ->where('provider_user_id', $providerUserId)
                ->first();

            if ($socialAccount) {
                $donor = $socialAccount->donor;

                $socialAccount->update([
                    'provider_email' => $email,
                    'provider_name' => $socialUser->getName(),
                    'avatar' => $socialUser->getAvatar(),
                    'access_token' => $socialUser->token ?? null,
                    'refresh_token' => $socialUser->refreshToken ?? null,
                    'token_expires_at' => isset($socialUser->expiresIn)
                        ? now()->addSeconds((int) $socialUser->expiresIn)
                        : null,
                ]);

                return $donor;
            }

            $donor = null;

            if ($email) {
                $donor = Donor::query()->where('email', $email)->first();
            }

            if (! $donor) {
                $donor = Donor::create([
                    'name' => $socialUser->getName() ?: null,
                    'email' => $email,
                    'password' => Hash::make(Str::random(40)),
                    'email_verified_at' => $email ? now() : null,
                ]);
            } else {
                $updates = [];

                if (empty($donor->name) && $socialUser->getName()) {
                    $updates['name'] = $socialUser->getName();
                }

                if (empty($donor->email_verified_at) && $email) {
                    $updates['email_verified_at'] = now();
                }

                if (! empty($updates)) {
                    $donor->update($updates);
                }
            }

            DonorSocialAccount::create([
                'donor_id' => $donor->id,
                'provider' => $provider,
                'provider_user_id' => $providerUserId,
                'provider_email' => $email,
                'provider_name' => $socialUser->getName(),
                'avatar' => $socialUser->getAvatar(),
                'access_token' => $socialUser->token ?? null,
                'refresh_token' => $socialUser->refreshToken ?? null,
                'token_expires_at' => isset($socialUser->expiresIn)
                    ? now()->addSeconds((int) $socialUser->expiresIn)
                    : null,
            ]);

            return $donor;
        });

        Auth::guard('donor')->login($donor, true);
        $request->session()->regenerate();

        // امسح أي redirect قديم يخص الأدمن أو غيره
        $request->session()->forget('url.intended');
        $locale = session()->pull('social_auth.locale', app()->getLocale());

        return redirect()->to(
            $locale === 'en'
                ? route('en.donor.dashboard')
                : route('donor.dashboard')
        );
    }
}
