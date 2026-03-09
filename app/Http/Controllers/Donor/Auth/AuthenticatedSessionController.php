<?php

namespace App\Http\Controllers\Donor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Donor\LoginDonorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('donor.auth.login');
    }

    public function store(LoginDonorRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return redirect()->to(locale_route('donor.dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('donor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('url.intended');

        return redirect()->to(locale_route('donor.login'));
    }
}
