<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Users for created_by
        $userIds = User::query()->pluck('id')->all();
        if (count($userIds) === 0) {
            $nowTs = now();
            DB::table('users')->insert([
                [
                    'name' => 'Reports Admin',
                    'email' => 'reports-admin@example.com',
                    'password' => bcrypt('password'),
                    'created_at' => $nowTs,
                    'updated_at' => $nowTs,
                ],
            ]);
            $userIds = User::query()->pluck('id')->all();
        }

        // Campaigns (optional link)
        $campaigns = DB::table('campaigns')->select('id', 'title_ar', 'title_en', 'status')->get();
        $campaignIds = $campaigns->pluck('id')->all(); // may be empty

        // Avoid duplicate reseed with batch tag stored in pdf_path pattern
        $batchId = 'report-seed-v1';
        $alreadySeeded = DB::table('reports')
            ->where('pdf_path', 'like', "reports/{$batchId}/%")
            ->exists();

        if ($alreadySeeded) {
            $this->command?->warn("ReportSeeder already ran for batch: {$batchId}. Change batchId to reseed.");
            return;
        }

        // Helper data
        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $years  = ['2024', '2025', '2026'];

        $templates = [
            [
                'title_ar' => 'تقرير التبرعات الشهري',
                'title_en' => 'Monthly Donations Report',
                'summary_ar' =>
                "يوثّق هذا التقرير أداء التبرعات خلال الفترة المحددة، ويتضمن:\n" .
                    "- إجمالي التبرعات المدفوعة\n" .
                    "- عدد العمليات الناجحة/المعلّقة/الفاشلة\n" .
                    "- ملخص الاستخدامات والخطط القادمة\n\n" .
                    "تم إعداد البيانات بناءً على السجلات المالية وتقارير فرق الميدان.",
                'summary_en' =>
                "This report documents donation performance during the selected period, including:\n" .
                    "- Total paid donations\n" .
                    "- Successful/pending/failed transactions count\n" .
                    "- A summary of usage and next steps\n\n" .
                    "Figures are compiled from financial logs and field team updates.",
            ],
            [
                'title_ar' => 'تقرير إنجازات الحملة',
                'title_en' => 'Campaign Progress Report',
                'summary_ar' =>
                "يركز هذا التقرير على تقدم الحملة ومؤشرات التنفيذ:\n" .
                    "- ما تم تنفيذه خلال الفترة\n" .
                    "- التحديات والعوائق\n" .
                    "- صور/مرفقات داعمة (ضمن ملف PDF)\n\n" .
                    "نلتزم بالشفافية ونشر ملخصات دورية للمستفيدين والداعمين.",
                'summary_en' =>
                "This report focuses on campaign progress and delivery indicators:\n" .
                    "- Activities completed during the period\n" .
                    "- Challenges and blockers\n" .
                    "- Supporting photos/attachments (within the PDF)\n\n" .
                    "We aim to maintain transparency with periodic updates for donors and beneficiaries.",
            ],
            [
                'title_ar' => 'تقرير مالي مختصر',
                'title_en' => 'Financial Summary Report',
                'summary_ar' =>
                "ملخص مالي يوضح التدفقات خلال الفترة:\n" .
                    "- إجمالي الإيرادات (تبرعات)\n" .
                    "- المصروفات التشغيلية والتوزيعات\n" .
                    "- الرصيد المرحّل\n\n" .
                    "يُرجى ملاحظة أن هذا التقرير ملخص، والتفاصيل متاحة عند الطلب للجهات المخوّلة.",
                'summary_en' =>
                "A financial snapshot for the period:\n" .
                    "- Total income (donations)\n" .
                    "- Operational expenses and distributions\n" .
                    "- Carried balance\n\n" .
                    "This is a summary; detailed breakdowns are available upon request for authorized parties.",
            ],
        ];

        $rows = [];

        /**
         * (A) تقارير شهرية عامة (بدون campaign_id أحياناً)
         * ننشئ مثلاً 18–30 تقرير موزع على سنوات/أشهر.
         */
        $monthlyCount = rand(18, 30);
        for ($i = 1; $i <= $monthlyCount; $i++) {
            $year = $years[array_rand($years)];
            $month = $months[array_rand($months)];

            $tpl = $templates[array_rand($templates)];

            $isPublic = (rand(1, 100) <= 85); // 85% public

            $createdAt = Carbon::createFromDate((int)$year, (int)$month, rand(1, 28))
                ->setTime(rand(9, 17), rand(0, 59));

            $updatedAt = $createdAt->copy()->addDays(rand(0, 10));

            $rows[] = [
                'title_ar' => $tpl['title_ar'] . " ({$month}/{$year})",
                'title_en' => $tpl['title_en'] . " ({$month}/{$year})",
                'summary_ar' => $tpl['summary_ar'],
                'summary_en' => $tpl['summary_en'],
                'period_month' => $month,
                'period_year' => $year,
                'campaign_id' => (rand(1, 100) <= 35 && count($campaignIds) > 0) ? $campaignIds[array_rand($campaignIds)] : null,
                'pdf_path' => $this->makePdfPath($batchId, $year, $month, null),
                'is_public' => $isPublic,
                'created_by' => $userIds[array_rand($userIds)],
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
        }

        /**
         * (B) تقارير خاصة بالحملات (Campaign-specific)
         * ننشئ لكل حملة عدد تقارير 0–4 حسب حالتها.
         */
        foreach ($campaigns as $c) {
            // حملات active/ended غالباً لها تقارير أكثر
            $count = match ($c->status) {
                'active' => rand(2, 5),
                'ended' => rand(1, 4),
                'paused' => rand(0, 3),
                'archived' => rand(1, 3),
                'draft' => rand(0, 1),
                default => rand(0, 2),
            };

            for ($j = 1; $j <= $count; $j++) {
                $year = $years[array_rand($years)];
                $month = $months[array_rand($months)];

                $baseAr = $c->title_ar ?: 'حملة';
                $baseEn = $c->title_en ?: 'Campaign';

                $titleAr = "تقرير الحملة: {$baseAr} ({$month}/{$year})";
                $titleEn = "Campaign Report: {$baseEn} ({$month}/{$year})";

                $summaryAr =
                    "ملخص سير العمل للحملة خلال الفترة المحددة:\n" .
                    "- تقدم التنفيذ والأنشطة المنجزة\n" .
                    "- أعداد المستفيدين (تقريبية) والتغطية الجغرافية\n" .
                    "- احتياجات المرحلة القادمة\n\n" .
                    "يتضمن ملف الـ PDF مرفقات داعمة وتوثيقاً متاحاً وفق سياسات الخصوصية.";

                $summaryEn =
                    "A progress summary for the campaign during the selected period:\n" .
                    "- Delivery progress and completed activities\n" .
                    "- Approx. beneficiaries and geographic coverage\n" .
                    "- Next phase needs\n\n" .
                    "The PDF includes supporting attachments and documentation subject to privacy policies.";

                $isPublic = (rand(1, 100) <= 75); // تقارير الحملات: 75% عامة

                $createdAt = Carbon::createFromDate((int)$year, (int)$month, rand(1, 28))
                    ->setTime(rand(9, 18), rand(0, 59));

                $updatedAt = $createdAt->copy()->addDays(rand(0, 14));

                $rows[] = [
                    'title_ar' => $titleAr,
                    'title_en' => $titleEn,
                    'summary_ar' => $summaryAr,
                    'summary_en' => $summaryEn,
                    'period_month' => $month,
                    'period_year' => $year,
                    'campaign_id' => $c->id,
                    'pdf_path' => $this->makePdfPath($batchId, $year, $month, $c->id),
                    'is_public' => $isPublic,
                    'created_by' => $userIds[array_rand($userIds)],
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];
            }
        }

        // إدخال على دفعات
        $chunkSize = 300;
        foreach (array_chunk($rows, $chunkSize) as $chunk) {
            DB::table('reports')->insert($chunk);
        }

        $this->command?->info('Seeded ' . count($rows) . " reports (batch {$batchId}).");
    }

    private function makePdfPath(string $batchId, string $year, string $month, ?int $campaignId): string
    {
        // مثال لمسار تخزين واقعي:
        // reports/report-seed-v1/2026/02/campaign-12/report-2026-02-xxxx.pdf
        $uuid = Str::lower(Str::random(12));
        $base = "reports/{$batchId}/{$year}/{$month}";

        if ($campaignId) {
            return "{$base}/campaign-{$campaignId}/report-{$year}-{$month}-{$uuid}.pdf";
        }

        return "{$base}/general/report-{$year}-{$month}-{$uuid}.pdf";
    }
}
