<?php

namespace Tests\Feature\Auth;

use App\Models\Donor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated_from_security_page(): void
    {
        $donor = Donor::create([
            'name' => 'Security Donor',
            'email' => 'security@example.com',
            'password' => Hash::make('OldSecure123!'),
        ]);

        $response = $this->actingAs($donor, 'donor')
            ->from('/account/security')
            ->put('/account/security', [
                'current_password' => 'OldSecure123!',
                'password' => 'NewSecure123!',
                'password_confirmation' => 'NewSecure123!',
            ]);

        $response->assertRedirect('/account/security');
        $response->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewSecure123!', $donor->fresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $donor = Donor::create([
            'name' => 'Security Donor',
            'email' => 'security@example.com',
            'password' => Hash::make('OldSecure123!'),
        ]);

        $response = $this->actingAs($donor, 'donor')
            ->from('/account/security')
            ->put('/account/security', [
                'current_password' => 'wrong-password',
                'password' => 'NewSecure123!',
                'password_confirmation' => 'NewSecure123!',
            ]);

        $response->assertRedirect('/account/security');
        $response->assertSessionHasErrors('current_password');
    }
}
