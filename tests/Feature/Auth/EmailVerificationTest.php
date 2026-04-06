<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_donor_can_access_dashboard_without_email_verification_step(): void
    {
        $response = $this->post('/register', [
            'name' => 'Verified Flow Donor',
            'email' => 'verified-flow@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticated('donor');
    }
}
