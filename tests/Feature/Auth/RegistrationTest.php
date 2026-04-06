<?php

namespace Tests\Feature\Auth;

use App\Models\Donor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_new_donors_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Donor',
            'email' => 'donor@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $this->assertAuthenticated('donor');
        $response->assertRedirect('/account');
        $this->assertDatabaseHas('donors', ['email' => 'donor@example.com']);
    }

    public function test_registered_donor_email_must_be_unique(): void
    {
        Donor::create([
            'name' => 'Existing Donor',
            'email' => 'donor@example.com',
            'password' => bcrypt('SecurePass123!'),
        ]);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Another Donor',
            'email' => 'donor@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
    }
}
