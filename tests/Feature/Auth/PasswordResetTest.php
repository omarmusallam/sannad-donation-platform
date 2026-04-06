<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_routes_are_not_exposed_in_current_donor_flow(): void
    {
        $this->get('/forgot-password')->assertNotFound();
        $this->get('/reset-password/test-token')->assertNotFound();
    }
}
