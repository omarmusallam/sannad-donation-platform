<?php

namespace Tests\Feature\Auth;

use App\Models\Donor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_screen_requires_authenticated_donor(): void
    {
        $this->get('/account/security')->assertRedirect('/login');
    }

    public function test_security_screen_can_be_rendered_for_authenticated_donor(): void
    {
        $donor = Donor::create([
            'name' => 'Security Donor',
            'email' => 'security-screen@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($donor, 'donor')
            ->get('/account/security')
            ->assertOk();
    }
}
