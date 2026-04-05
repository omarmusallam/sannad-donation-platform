<?php

namespace App\Services\Payments;

use App\Models\Donation;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeCheckoutService
{
    public function redirectToCheckout(Donation $donation)
    {
        $secret = config('services.stripe.secret');

        if (!$secret) {
            abort(500, 'Stripe secret key is missing.');
        }

        Stripe::setApiKey($secret);

        $donation->loadMissing('campaign');

        $locale = app()->getLocale();
        $currency = strtolower($donation->currency);
        $amount = (int) round(((float) $donation->amount) * 100);

        $campaignTitle = $locale === 'en'
            ? ($donation->campaign?->title_en ?: $donation->campaign?->title_ar ?: 'Campaign donation')
            : ($donation->campaign?->title_ar ?: $donation->campaign?->title_en ?: 'تبرع');

        $metadata = [
            'donation_id' => (string) $donation->id,
            'campaign_id' => (string) $donation->campaign_id,
            'payment_type' => 'donation',
        ];

        $session = Session::create([
            'mode' => 'payment',
            'success_url' => locale_route('donate.success', ['donation' => $donation->public_id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => locale_route('donate.cancel', ['donation' => $donation->public_id]),
            'payment_method_types' => ['card'],
            'customer_email' => $donation->donor_email ?: null,
            'client_reference_id' => (string) $donation->id,
            'metadata' => $metadata,
            'payment_intent_data' => [
                'metadata' => $metadata,
            ],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => $amount,
                    'product_data' => [
                        'name' => $locale === 'en' ? 'Donation' : 'تبرع',
                        'description' => $campaignTitle,
                    ],
                ],
            ]],
        ]);

        $donation->update([
            'provider' => 'stripe',
            'provider_ref' => $session->id,
        ]);

        return redirect()->away($session->url);
    }
}
