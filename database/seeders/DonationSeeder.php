<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DonationSeeder extends Seeder
{
    private const DEFAULT_CURRENCY = 'USD';

    public function run(): void
    {
        // إذا لا يوجد حملات، الأفضل تشغيل CampaignSeeder أولاً
        $campaigns = DB::table('campaigns')
            ->select('id', 'goal_amount', 'status')
            ->get();

        if ($campaigns->count() === 0) {
            $this->command?->warn('No campaigns found. Run CampaignSeeder first.');
            return;
        }

        $now = Carbon::now();

        /**
         * لتجنب تكرار نفس البيانات عند إعادة تشغيل seeder:
         * غيّر هذا الباتش مرة واحدة أو خلّيه ثابت وتشغله مرة واحدة فقط.
         */
        $batchId = 'donation-seed-v1';

        // لو تحب تمنع إعادة الإدخال لنفس batch بدون تعديل DB:
        // نتحقق هل سبق وزرعنا هذا batch عبر provider_ref pattern
        $alreadySeeded = DB::table('donations')
            ->where('provider', 'seeder')
            ->where('provider_ref', 'like', $batchId . ':%')
            ->exists();

        if ($alreadySeeded) {
            $this->command?->warn("DonationSeeder already ran for batch: {$batchId}. Change batchId to reseed.");
            return;
        }

        // إعدادات أسماء/إيميلات شبه حقيقية
        $firstNames = ['Ahmad', 'Mohammed', 'Omar', 'Yousef', 'Khaled', 'Sara', 'Lina', 'Noor', 'Rana', 'Heba', 'Maha', 'Tariq', 'Hani', 'Alaa', 'Dina'];
        $lastNames  = ['Salem', 'Nassar', 'Karmi', 'Haddad', 'Masri', 'Barakat', 'Sabbah', 'Awad', 'Qasem', 'Hamdan', 'Shamia', 'Jaber', 'Yasin', 'Kanaan', 'Darwish'];
        $domains    = ['gmail.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'proton.me'];

        // مبالغ متنوعة (أنت حر تزيد/تعدل)
        $amountBands = [
            [5, 25],
            [25, 100],
            [100, 500],
            [500, 2000],
        ];

        // توزيع statuses (تقريبياً)
        // paid أكبر نسبة، ثم pending، ثم failed، ثم refunded
        $statusPool = array_merge(
            array_fill(0, 70, 'paid'),
            array_fill(0, 15, 'pending'),
            array_fill(0, 10, 'failed'),
            array_fill(0, 5, 'refunded'),
        );

        $paymentPool = array_merge(
            array_fill(0, 82, 'card'),
            array_fill(0, 18, 'usdt_trc20')
        );

        $donationsRows = [];

        // عدد التبرعات الإجمالي (كبير)
        // مثال: من 1200 إلى 2200 تبرع
        $totalDonations = rand(1200, 2200);

        // توزيع التبرعات على الحملات بشكل غير متساوٍ (حملات أكثر شعبية)
        // سنصنع "weights" بسيطة بناءً على status + featured؟ (غير موجود هنا) => سنعتمد status فقط
        $campaignIdsWeighted = [];
        foreach ($campaigns as $c) {
            $weight = match ($c->status) {
                'active' => 8,
                'paused' => 4,
                'ended' => 3,
                'draft' => 1,
                'archived' => 2,
                default => 2,
            };

            for ($i = 0; $i < $weight; $i++) {
                $campaignIdsWeighted[] = $c->id;
            }
        }

        // سنجمّع مبالغ المدفوعة لكل حملة لتحديث current_amount
        $paidSumsByCampaign = [];

        for ($i = 1; $i <= $totalDonations; $i++) {
            $campaignId = $campaignIdsWeighted[array_rand($campaignIdsWeighted)];
            $campaign = $campaigns->firstWhere('id', $campaignId);

            $status = $statusPool[array_rand($statusPool)];
            $paymentMethod = $paymentPool[array_rand($paymentPool)];

            // Anonymous logic
            $isAnonymous = (rand(1, 100) <= 18); // 18% مجهول
            $donorName = null;
            $donorEmail = null;

            if (!$isAnonymous) {
                $fn = $firstNames[array_rand($firstNames)];
                $ln = $lastNames[array_rand($lastNames)];
                $donorName = "{$fn} {$ln}";

                // 80% يكون فيه إيميل، 20% بدون
                if (rand(1, 100) <= 80) {
                    $domain = $domains[array_rand($domains)];
                    $local = Str::slug($fn . '.' . $ln, '.');
                    $donorEmail = $local . rand(10, 9999) . '@' . $domain;
                }
            } else {
                // مجهول: غالباً لا اسم/لا ايميل، لكن ممكن donor_email موجود (اختياري)
                if (rand(1, 100) <= 10) {
                    $donorEmail = 'anonymous' . rand(10, 9999) . '@' . $domains[array_rand($domains)];
                }
            }

            // Amount logic:
            // pending/failed عادة أقل، paid/refunded متنوعة
            $band = $amountBands[array_rand($amountBands)];
            $amount = rand($band[0] * 100, $band[1] * 100) / 100; // 2 decimals

            if ($status === 'failed') {
                // فشل غالباً مبالغ صغيرة
                $amount = rand(5 * 100, 50 * 100) / 100;
            }

            $currency = self::DEFAULT_CURRENCY;

            // provider/provider_ref:
            // - paid/refunded: غالباً provider موجود
            // - pending: أحياناً provider موجود (حجز)
            // - failed: أحياناً موجود
            $provider = null;
            $providerRef = null;

            $shouldHaveProvider = match ($status) {
                'paid' => rand(1, 100) <= 75,
                'refunded' => rand(1, 100) <= 90,
                'pending' => rand(1, 100) <= 40,
                'failed' => rand(1, 100) <= 50,
                default => false,
            };

            if ($shouldHaveProvider) {
                $provider = $paymentMethod === 'card'
                    ? ((rand(1, 100) <= 60) ? 'stripe' : 'seeder')
                    : 'wallet';
                // نضمن uniqueness داخل هذا batch
                $providerRef = $batchId . ':' . Str::uuid()->toString();
            }

            // paid_at logic:
            $paidAt = null;
            if ($status === 'paid') {
                // مدفوع خلال آخر 120 يوم
                $paidAt = $now->copy()->subDays(rand(0, 120))->subMinutes(rand(0, 1440));
            } elseif ($status === 'refunded') {
                // كان مدفوع ثم تم استرجاعه: paid_at موجود وأقدم غالباً
                $paidAt = $now->copy()->subDays(rand(10, 180))->subMinutes(rand(0, 1440));
            }

            $createdAt = $paidAt
                ? $paidAt->copy()->subMinutes(rand(1, 60))
                : $now->copy()->subDays(rand(0, 180))->subMinutes(rand(0, 1440));

            $updatedAt = $createdAt->copy()->addMinutes(rand(0, 240));
            $refundedAt = $status === 'refunded' && $paidAt
                ? $paidAt->copy()->addMinutes(rand(30, 2880))
                : null;
            $cryptoSubmittedAt = $paymentMethod === 'usdt_trc20'
                ? $createdAt->copy()->addMinutes(rand(1, 45))
                : null;
            $cryptoTxHash = $paymentMethod === 'usdt_trc20'
                ? str_replace('-', '', (string) Str::uuid()) . str_replace('-', '', (string) Str::uuid())
                : null;
            $cryptoSenderWallet = $paymentMethod === 'usdt_trc20' && rand(1, 100) <= 70
                ? 'T' . Str::upper(Str::random(33))
                : null;

            $donationsRows[] = [
                'public_id' => (string) Str::uuid(),
                'campaign_id' => $campaignId,
                'donor_name' => $donorName,
                'donor_email' => $donorEmail,
                'is_anonymous' => $isAnonymous,
                'amount' => $amount,
                'fees' => 0,
                'net_amount' => in_array($status, ['paid', 'refunded'], true) ? $amount : null,
                'currency' => $currency,
                'payment_method' => $paymentMethod,
                'status' => $status,
                'provider' => $provider,
                'provider_ref' => $providerRef,
                'crypto_network' => $paymentMethod === 'usdt_trc20' ? 'trc20' : null,
                'crypto_wallet_address' => $paymentMethod === 'usdt_trc20' ? config('services.crypto.usdt_trc20_wallet') : null,
                'crypto_tx_hash' => $cryptoTxHash,
                'crypto_sender_wallet' => $cryptoSenderWallet,
                'crypto_submitted_at' => $cryptoSubmittedAt,
                'refunded_at' => $refundedAt,
                'paid_at' => $paidAt,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];

            // جمع المدفوع فقط ليُحتسب في current_amount
            if ($status === 'paid') {
                $paidSumsByCampaign[$campaignId] = ($paidSumsByCampaign[$campaignId] ?? 0) + $amount;
            }
        }

        // إدخال تبرعات على دفعات لتقليل الضغط على الذاكرة/الوقت
        $chunkSize = 500;
        foreach (array_chunk($donationsRows, $chunkSize) as $chunk) {
            DB::table('donations')->insert($chunk);
        }

        // تحديث current_amount لكل حملة بناءً على التبرعات المدفوعة
        // (مهم جداً لأنه عندك عمود current_amount في campaigns)
        foreach ($paidSumsByCampaign as $campaignId => $paidSum) {
            // إذا تحب: لا تتجاوز goal_amount إلا إذا تحب السماح بتجاوز الهدف
            $goal = (float) DB::table('campaigns')->where('id', $campaignId)->value('goal_amount');
            $newCurrent = round(min($paidSum, $goal), 2);

            DB::table('campaigns')
                ->where('id', $campaignId)
                ->update([
                    'current_amount' => $newCurrent,
                    'updated_at' => now(),
                ]);
        }

        $this->command?->info("Seeded {$totalDonations} donations (batch {$batchId}) and updated campaigns.current_amount.");
    }
}
