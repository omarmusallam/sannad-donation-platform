<?php

namespace Tests\Feature;

use App\Models\Donor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed_for_authenticated_donor(): void
    {
        $donor = Donor::create([
            'name' => 'Profile Donor',
            'email' => 'profile@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($donor, 'donor')
            ->get('/account/profile')
            ->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $donor = Donor::create([
            'name' => 'Profile Donor',
            'email' => 'profile@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($donor, 'donor')
            ->from('/account/profile')
            ->put('/account/profile', [
                'name' => 'Updated Donor',
                'email' => 'updated@example.com',
                'phone' => '+970500000000',
                'country' => 'Palestine',
            ]);

        $response->assertRedirect('/account/profile');
        $response->assertSessionHasNoErrors();

        $donor->refresh();

        $this->assertSame('Updated Donor', $donor->name);
        $this->assertSame('updated@example.com', $donor->email);
        $this->assertSame('+970500000000', $donor->phone);
        $this->assertSame('Palestine', $donor->country);
    }

    public function test_guest_is_redirected_away_from_profile_page(): void
    {
        $this->get('/account/profile')->assertRedirect('/login');
    }
}
