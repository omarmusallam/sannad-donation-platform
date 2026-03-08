<?php

namespace App\Http\Controllers\Donor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('donor.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::guard('donor')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // امسح أي intended قديم قد يخص الأدمن
        $request->session()->forget('url.intended');

        return redirect()->to(locale_route('donor.dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('donor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(locale_route('donor.login'));
    }
}
