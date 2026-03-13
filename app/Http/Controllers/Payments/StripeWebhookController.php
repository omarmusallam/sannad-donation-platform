<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Receipt;
use App\Services\ReceiptPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, ReceiptPdfService $pdfService)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (\UnexpectedValueException | SignatureVerificationException $e) {
            Log::warning('Stripe webhook invalid.', [
                'message' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Invalid webhook'], 400);
        }

        if ($event->type !== 'checkout.session.completed') {
            return response()->json(['received' => true]);
        }

        $session = $event->data->object;
        $donationId = data_get($session, 'metadata.donation_id');

        if (!$donationId) {
            Log::warning('Stripe webhook missing donation id.', [
                'session_id' => $session->id ?? null,
            ]);

            return response()->json(['message' => 'Missing donation id'], 422);
        }

        if (($session->payment_status ?? null) !== 'paid') {
            Log::warning('Stripe checkout session completed but not paid.', [
                'session_id' => $session->id ?? null,
                'payment_status' => $session->payment_status ?? null,
            ]);

            return response()->json(['received' => true]);
        }

        DB::transaction(function () use ($donationId, $session, $pdfService) {
            $donation = Donation::with(['campaign', 'receipt'])
                ->lockForUpdate()
                ->find($donationId);

            if (!$donation) {
                Log::warning('Stripe webhook donation not found.', [
                    'donation_id' => $donationId,
                    'session_id' => $session->id ?? null,
                ]);

                return;
            }

            if ($donation->status === 'paid') {
                return;
            }

            $donation->update([
                'status' => 'paid',
                'provider' => 'stripe',
                'provider_ref' => $session->id,
                'fees' => 0,
                'net_amount' => $donation->amount,
                'paid_at' => now(),
            ]);

            $donation->campaign()->increment('current_amount', $donation->amount);

            $receipt = $donation->receipt ?: Receipt::create([
                'uuid' => (string) Str::uuid(),
                'receipt_no' => 'RC-' . now()->format('Ymd') . '-' . str_pad($donation->id, 6, '0', STR_PAD_LEFT),
                'donation_id' => $donation->id,
                'donor_name' => $donation->donor_name,
                'donor_email' => $donation->donor_email,
                'amount' => $donation->amount,
                'currency' => $donation->currency,
                'status' => 'issued',
                'issued_at' => now(),
            ]);

            if (!$receipt->pdf_path) {
                $pdfPath = $pdfService->buildAndStore($receipt);
                $receipt->update(['pdf_path' => $pdfPath]);
            }
        });

        return response()->json(['received' => true]);
    }
}
