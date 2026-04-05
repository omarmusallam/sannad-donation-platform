<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicDonationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_donation_submission_accepts_usd_only(): void
    {
        $campaign = Campaign::create([
            'title_ar' => 'حملة اختبار',
            'title_en' => 'Test Campaign',
            'slug' => 'test-campaign',
            'goal_amount' => 1000,
            'currency' => 'EUR',
            'status' => 'active',
        ]);

        $response = $this->from('/donate')->post('/donate', [
            'campaign_id' => $campaign->id,
            'amount' => 50,
            'currency' => 'EUR',
            'payment_method' => 'card',
            'donor_email' => 'guest@example.com',
        ]);

        $response->assertRedirect('/donate');
        $response->assertSessionHasErrors('currency');
        $this->assertDatabaseCount('donations', 0);
    }

    public function test_guest_crypto_donation_requires_email_for_receipt_follow_up(): void
    {
        $campaign = Campaign::create([
            'title_ar' => 'حملة اختبار',
            'title_en' => 'Test Campaign',
            'slug' => 'crypto-campaign',
            'goal_amount' => 1000,
            'status' => 'active',
        ]);

        $response = $this->from('/donate')->post('/donate', [
            'campaign_id' => $campaign->id,
            'amount' => 25,
            'currency' => 'USD',
            'payment_method' => 'usdt_trc20',
        ]);

        $response->assertRedirect('/donate');
        $response->assertSessionHasErrors('donor_email');
        $this->assertDatabaseCount('donations', 0);
    }

    public function test_public_donation_status_uses_public_id_not_sequential_id(): void
    {
        $campaign = Campaign::create([
            'title_ar' => 'حملة اختبار',
            'title_en' => 'Test Campaign',
            'slug' => 'status-campaign',
            'goal_amount' => 1000,
            'status' => 'active',
        ]);

        $donation = Donation::create([
            'public_id' => (string) Str::uuid(),
            'campaign_id' => $campaign->id,
            'amount' => 10,
            'fees' => 0,
            'currency' => 'USD',
            'payment_method' => 'card',
            'status' => 'pending',
        ]);

        $this->get('/donate/status/' . $donation->public_id)->assertOk();
        $this->get('/donate/status/' . $donation->id)->assertNotFound();
    }
}
