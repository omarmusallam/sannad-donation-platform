<?php

namespace App\Http\Controllers\Donor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Donor\RegisterDonorRequest;
use App\Models\Donor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('donor.auth.register');
    }

    public function store(RegisterDonorRequest $request)
    {
        $data = $request->validated();

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
