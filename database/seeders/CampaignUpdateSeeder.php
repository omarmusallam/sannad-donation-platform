<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CampaignUpdateSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Users (created_by)
        $userIds = User::query()->pluck('id')->all();
        if (count($userIds) === 0) {
            $ts = now();
            DB::table('users')->insert([
                [
                    'name' => 'Updates Admin',
                    'email' => 'updates-admin@example.com',
                    'password' => bcrypt('password'),
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ],
            ]);
            $userIds = User::query()->pluck('id')->all();
        }

        // Campaigns
        $campaigns = DB::table('campaigns')
            ->select('id', 'title_ar', 'title_en', 'status', 'starts_at', 'ends_at', 'created_at')
            ->get();

        if ($campaigns->count() === 0) {
            $this->command?->warn('No campaigns found. Run CampaignSeeder first.');
            return;
        }

        // Prevent duplicate reseed
        $batchId = 'campaign-updates-seed-v1';
        $alreadySeeded = DB::table('campaign_updates')
            ->where('title_en', 'like', "[{$batchId}]%")
            ->orWhere('title_ar', 'like', "[{$batchId}]%")
            ->exists();

        if ($alreadySeeded) {
            $this->command?->warn("CampaignUpdateSeeder already ran for batch: {$batchId}. Change batchId to reseed.");
            return;
        }

        // Templates (Arabic/English) — واقعية + قابلة للتدوير
        $titleTemplates = [
            ['ar' => 'تحديث ميداني', 'en' => 'Field Update'],
            ['ar' => 'تقرير تقدم التنفيذ', 'en' => 'Implementation Progress'],
            ['ar' => 'تحديث التوريد والشراء', 'en' => 'Procurement Update'],
            ['ar' => 'ملخص أسبوعي', 'en' => 'Weekly Summary'],
            ['ar' => 'تحديث التوزيعات', 'en' => 'Distribution Update'],
            ['ar' => 'مستجدات مهمة', 'en' => 'Key Update'],
        ];

        $bodyBlocksAr = [
            "ما الذي تم إنجازه:\n- استكمال تجهيز قائمة المستفيدين.\n- التنسيق مع الشركاء المحليين.\n- تجهيز خطة التوزيع وفق الأولويات.\n\nما القادم:\n- بدء التوزيع على دفعات.\n- متابعة التوثيق وإصدار تقرير موجز.",
            "تحديث المشتريات:\n- تم تحديد الموردين واعتماد عروض الأسعار.\n- شراء دفعة أولى من المستلزمات الأساسية.\n- فحص الجودة قبل الاستلام.\n\nملاحظة:\nنعمل على تقليل التكلفة عبر الشراء بالجملة دون التأثير على الجودة.",
            "تحديث التنفيذ:\n- تنفيذ نشاط ميداني في نقطتين.\n- تنظيم آلية الاستلام لضمان العدالة.\n- توثيق عمليات التسليم وفق سياسة الخصوصية.\n\nالتحديات:\nازدحام في بعض النقاط وتم التعامل معه بزيادة عدد المتطوعين.",
            "تحديث التوزيع:\n- تم تسليم دفعة جديدة من المساعدات.\n- معالجة حالات خاصة بالتنسيق مع الجهات المختصة.\n\nالشفافية:\nسيتم نشر ملخص أرقام المستفيدين في نهاية الفترة.",
            "ملخص أسبوعي:\n- تقدم جيد ضمن الخطة.\n- تم حل عائق لوجستي متعلق بالنقل.\n- تحديث قوائم المستفيدين بعد التحقق.\n\nالخطوة التالية:\nالتحضير للدفعة القادمة خلال الأيام المقبلة.",
        ];

        $bodyBlocksEn = [
            "What we completed:\n- Finalized the beneficiary shortlist.\n- Coordinated with local partners.\n- Prepared distribution plan based on priorities.\n\nNext steps:\n- Start phased distribution.\n- Continue documentation and publish a brief report.",
            "Procurement update:\n- Shortlisted suppliers and approved quotations.\n- Purchased the first batch of essential items.\n- Performed quality checks before receipt.\n\nNote:\nWe aim to reduce costs through bulk purchasing without compromising quality.",
            "Implementation update:\n- Delivered field activities at two points.\n- Organized a fair receiving process.\n- Documented deliveries in line with privacy policy.\n\nChallenges:\nCrowding at some locations was mitigated by adding volunteers.",
            "Distribution update:\n- Delivered a new batch of assistance.\n- Handled special cases with relevant stakeholders.\n\nTransparency:\nA beneficiary count summary will be published at the end of the period.",
            "Weekly summary:\n- Good progress on track.\n- Resolved a logistics issue related to transportation.\n- Updated beneficiary lists after verification.\n\nNext:\nPrepare the next batch in the coming days.",
        ];

        $rows = [];

        foreach ($campaigns as $c) {
            // عدد التحديثات حسب حالة الحملة
            $updatesCount = match ($c->status) {
                'active' => rand(5, 12),
                'paused' => rand(2, 7),
                'ended' => rand(2, 6),
                'archived' => rand(1, 4),
                'draft' => rand(0, 2),
                default => rand(1, 5),
            };

            // baseline dates
            $startBase = $c->starts_at ? Carbon::parse($c->starts_at) : Carbon::parse($c->created_at);
            $endBase = $c->ends_at ? Carbon::parse($c->ends_at) : $now;

            // range: من startBase إلى min(now, endBase + buffer بسيط)
            $rangeEnd = $endBase->copy();
            if ($rangeEnd->greaterThan($now)) {
                $rangeEnd = $now->copy();
            }

            // لو start بعد rangeEnd (نادر) نعالج
            if ($startBase->greaterThan($rangeEnd)) {
                $startBase = $rangeEnd->copy()->subDays(rand(3, 20));
            }

            for ($i = 1; $i <= $updatesCount; $i++) {
                $tpl = $titleTemplates[array_rand($titleTemplates)];
                $baseAr = $tpl['ar'];
                $baseEn = $tpl['en'];

                $campaignNameAr = $c->title_ar ?: 'الحملة';
                $campaignNameEn = $c->title_en ?: 'Campaign';

                // public/scheduled logic
                $isPublic = (rand(1, 100) <= 80); // 80% public
                $isScheduled = (rand(1, 100) <= 12); // 12% مجدول للمستقبل (إن كانت الحملة active/paused غالباً)
                if (!in_array($c->status, ['active', 'paused'], true)) {
                    $isScheduled = false;
                }

                // published_at:
                // - لو مجدول: بعد الآن بـ 1-20 يوم
                // - لو غير مجدول: داخل فترة الحملة
                if ($isScheduled) {
                    $publishedAt = $now->copy()->addDays(rand(1, 20))->setTime(rand(9, 19), rand(0, 59));
                } else {
                    $publishedAt = $this->randomDateBetween($startBase, $rangeEnd);
                }

                // bodies
                $bodyAr = $bodyBlocksAr[array_rand($bodyBlocksAr)];
                $bodyEn = $bodyBlocksEn[array_rand($bodyBlocksEn)];

                // تخصيص بسيط داخل النص (واقعي)
                $bodyAr .= "\n\nمعلومات إضافية:\n- رقم الدفعة: " . rand(1, 8) . "\n- عدد المتطوعين: " . rand(4, 25);
                $bodyEn .= "\n\nAdditional details:\n- Batch #: " . rand(1, 8) . "\n- Volunteers: " . rand(4, 25);

                // timestamps
                $createdAt = $publishedAt ? Carbon::parse($publishedAt)->copy()->subMinutes(rand(5, 240)) : $now->copy()->subDays(rand(1, 30));
                $updatedAt = $createdAt->copy()->addMinutes(rand(0, 240));

                // نضيف batch tag في العنوان حتى نقدر نمنع التكرار
                $titleAr = "[{$batchId}] {$baseAr}: {$campaignNameAr}";
                $titleEn = "[{$batchId}] {$baseEn}: {$campaignNameEn}";

                $rows[] = [
                    'campaign_id' => $c->id,
                    'title_ar' => $titleAr,
                    'title_en' => $titleEn,
                    'body_ar' => $bodyAr,
                    'body_en' => $bodyEn,
                    'is_public' => $isPublic,
                    'published_at' => $publishedAt,
                    'created_by' => $userIds[array_rand($userIds)],
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];
            }
        }

        // إدخال على دفعات
        $chunkSize = 400;
        foreach (array_chunk($rows, $chunkSize) as $chunk) {
            DB::table('campaign_updates')->insert($chunk);
        }

        $this->command?->info('Seeded ' . count($rows) . " campaign updates (batch {$batchId}).");
    }

    private function randomDateBetween(Carbon $start, Carbon $end): Carbon
    {
        $startTs = $start->getTimestamp();
        $endTs = $end->getTimestamp();

        if ($endTs <= $startTs) {
            return $start->copy();
        }

        $randTs = rand($startTs, $endTs);
        return Carbon::createFromTimestamp($randTs)->setTimezone($start->getTimezone());
    }
}
