<?php

namespace Tests\Feature\Auth;

use App\Models\Donor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_donors_can_authenticate_using_the_login_screen(): void
    {
        $donor = Donor::create([
            'name' => 'Test Donor',
            'email' => 'donor@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $donor->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('donor');
        $response->assertRedirect('/account');
    }

    public function test_donors_can_not_authenticate_with_invalid_password(): void
    {
        $donor = Donor::create([
            'name' => 'Test Donor',
            'email' => 'donor@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->from('/login')->post('/login', [
            'email' => $donor->email,
            'password' => 'wrong-password',
        ])->assertRedirect('/login');

        $this->assertGuest('donor');
    }

    public function test_donors_can_logout(): void
    {
        $donor = Donor::create([
            'name' => 'Test Donor',
            'email' => 'donor@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($donor, 'donor')->post('/logout');

        $this->assertGuest('donor');
        $response->assertRedirect('/login');
    }
}
