<?php

namespace App\Http\Controllers\Donor\Auth;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('donor.auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:donors,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $donor = Donor::create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('donor')->login($donor);

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return redirect()->to(locale_route('donor.dashboard'));
    }
}
