<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        // 1) جهّز Users (created_by) - لو ما عندك Users أنشئ كم واحد Dummy
        $userIds = User::query()->pluck('id')->all();
        if (count($userIds) === 0) {
            // إنشاء مستخدمين تجريبيين بسرعة (بدون الاعتماد على Factory)
            $now = now();
            DB::table('users')->insert([
                [
                    'name' => 'Admin Seeder',
                    'email' => 'admin-seeder@example.com',
                    'password' => bcrypt('password'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Manager Seeder',
                    'email' => 'manager-seeder@example.com',
                    'password' => bcrypt('password'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            $userIds = User::query()->pluck('id')->all();
        }

        // 2) إعدادات عامة
        $now = Carbon::now();
        $currencies = ['USD', 'EUR', 'ILS', 'JOD', 'GBP'];

        // 3) بيانات شبه واقعية (عناوين + وصف عربي/انجليزي)
        $items = [
            [
                'title_ar' => 'حملة كفالة أسر متضررة في غزة',
                'title_en' => 'Sponsorship for Affected Families in Gaza',
                'description_ar' => "هذه الحملة تهدف لتقديم كفالات شهرية للأسر الأكثر تضرراً.\n\nتشمل المساعدة: سلال غذائية، احتياجات صحية، ودعم إيجار.\n\nسيتم توثيق التوزيع بتقارير دورية وصور وفواتير قدر الإمكان.",
                'description_en' => "This campaign provides monthly sponsorships to the most affected families.\n\nSupport includes: food parcels, basic medical needs, and rent assistance.\n\nDistribution will be documented through periodic reports and evidence when possible.",
                'cover' => 'campaigns/covers/gaza-families.jpg',
                'goal'  => 150000,
                'featured' => true,
                'priority' => 90,
                'status' => 'active',
            ],
            [
                'title_ar' => 'تجهيز قسم طوارئ في مستشفى محلي',
                'title_en' => 'Equipping an Emergency Department',
                'description_ar' => "نسعى لتجهيز قسم الطوارئ بمستلزمات أساسية: أجهزة مراقبة، أسِرّة، وأدوية طارئة.\n\nالخطة:\n1) حصر الاحتياج بالتنسيق مع الإدارة.\n2) شراء المعدات من موردين معتمدين.\n3) تسليم واستلام رسمي وتوثيق.",
                'description_en' => "We aim to equip an emergency department with essentials: monitors, beds, and emergency medicines.\n\nPlan:\n1) Needs assessment with hospital management.\n2) Procurement from trusted suppliers.\n3) Official delivery and documentation.",
                'cover' => 'campaigns/covers/emergency-dept.jpg',
                'goal'  => 220000,
                'featured' => true,
                'priority' => 80,
                'status' => 'active',
            ],
            [
                'title_ar' => 'حقيبة مدرسية لـ 2000 طالب',
                'title_en' => 'School Kits for 2,000 Students',
                'description_ar' => "هدفنا توفير حقائب مدرسية متكاملة (دفاتر، أقلام، أدوات هندسية) لـ 2000 طالب.\n\nمعايير الاختيار:\n- أولوية للأسر ذات الدخل المحدود.\n- توثيق عبر كشوفات الاستلام.\n\nسيُنشر تقرير ختامي عند اكتمال التوزيع.",
                'description_en' => "We plan to distribute complete school kits (notebooks, pens, geometry tools) to 2,000 students.\n\nSelection criteria:\n- Priority for low-income families.\n- Documentation via signed receiving lists.\n\nA final report will be published upon completion.",
                'cover' => 'campaigns/covers/school-kits.jpg',
                'goal'  => 60000,
                'featured' => false,
                'priority' => 55,
                'status' => 'paused',
            ],
            [
                'title_ar' => 'سلال غذائية شهرية للأسر المحتاجة',
                'title_en' => 'Monthly Food Baskets for Families',
                'description_ar' => "توزيع سلال غذائية شهرية تحتوي على مواد أساسية (رز، طحين، زيت، بقوليات).\n\nآلية التنفيذ:\n- شراء بالجملة لتقليل التكلفة.\n- نقاط توزيع ثابتة مع تنظيم الدور.\n- توثيق بالصور وبيانات المستفيدين.",
                'description_en' => "Monthly food baskets with staples (rice, flour, oil, legumes).\n\nExecution:\n- Bulk procurement to reduce costs.\n- Fixed distribution points with queue management.\n- Documentation with photos and beneficiary records.",
                'cover' => 'campaigns/covers/food-baskets.jpg',
                'goal'  => 95000,
                'featured' => false,
                'priority' => 60,
                'status' => 'active',
            ],
            [
                'title_ar' => 'ترميم منازل متضررة (المرحلة الأولى)',
                'title_en' => 'Home Repairs (Phase 1)',
                'description_ar' => "المرحلة الأولى تستهدف ترميم 25 منزلاً: أبواب/نوافذ/دهانات/تمديدات كهرباء بسيطة.\n\nالمخرجات:\n- كشف فني لكل منزل.\n- فواتير مواد.\n- توثيق قبل/بعد.",
                'description_en' => "Phase 1 targets repairing 25 homes: doors/windows/painting/basic electrical fixes.\n\nDeliverables:\n- Technical assessment per home.\n- Material invoices.\n- Before/after documentation.",
                'cover' => 'campaigns/covers/home-repairs.jpg',
                'goal'  => 180000,
                'featured' => true,
                'priority' => 75,
                'status' => 'ended',
            ],
            [
                'title_ar' => 'حفر بئر مياه في منطقة ريفية',
                'title_en' => 'Water Well for a Rural Area',
                'description_ar' => "حفر بئر مياه مع مضخة وخزان لتوفير مصدر مياه مستدام.\n\nخطوات العمل:\n1) دراسة جيولوجية.\n2) استخراج التصاريح.\n3) الحفر والتركيب.\n4) فحص جودة المياه.\n\nسيتم نشر تقرير هندسي بعد التنفيذ.",
                'description_en' => "Drilling a well with pump and tank to provide sustainable water access.\n\nSteps:\n1) Geological survey.\n2) Permits.\n3) Drilling & installation.\n4) Water quality testing.\n\nAn engineering report will be published after completion.",
                'cover' => 'campaigns/covers/water-well.jpg',
                'goal'  => 130000,
                'featured' => false,
                'priority' => 50,
                'status' => 'draft',
            ],
            [
                'title_ar' => 'عيادة متنقلة لخدمة القرى البعيدة',
                'title_en' => 'Mobile Clinic for Remote Villages',
                'description_ar' => "تجهيز عيادة متنقلة (فحوصات أولية، أدوية أساسية، حملات توعية).\n\nالنتائج المتوقعة:\n- 3000 مستفيد خلال 3 أشهر.\n- تقارير أسبوعية بعدد الحالات والخدمات.",
                'description_en' => "Equipping a mobile clinic (basic checkups, essential meds, awareness sessions).\n\nExpected outcomes:\n- 3,000 beneficiaries in 3 months.\n- Weekly reports on cases and services delivered.",
                'cover' => 'campaigns/covers/mobile-clinic.jpg',
                'goal'  => 210000,
                'featured' => true,
                'priority' => 85,
                'status' => 'archived',
            ],
        ];

        // 4) بناء السجلات مع منطق مالي/زمني + slug unique
        $rows = [];
        foreach ($items as $i => $item) {

            $status = $item['status'];

            // عمل slug من العنوان العربي (ولو فشل، fallback بالانجليزي)
            $baseSlugSource = $item['title_en'] ?: $item['title_ar'];
            $baseSlug = Str::slug($baseSlugSource);

            // fallback إضافي لو Str::slug أعطى فارغ بسبب العربي بالكامل
            if (!$baseSlug) {
                $baseSlug = 'campaign-' . ($i + 1);
            }

            // ضمان uniqueness حتى لو تكررت العناوين
            $slug = $this->uniqueSlug($baseSlug);

            $goal = (float) $item['goal'];
            $current = $this->makeCurrentAmount($goal, $status);

            // تواريخ منطقية حسب الحالة
            [$startsAt, $endsAt] = $this->makeDates($now, $status);

            $rows[] = [
                'title_ar' => $item['title_ar'],
                'title_en' => $item['title_en'],
                'slug' => $slug,
                'description_ar' => $item['description_ar'],
                'description_en' => $item['description_en'],
                'goal_amount' => $goal,
                'current_amount' => $current,
                'currency' => $currencies[array_rand($currencies)],
                'status' => $status,
                'is_featured' => (bool) $item['featured'],
                'priority' => (int) $item['priority'],
                'cover_image_path' => $item['cover'],
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'created_by' => $userIds[array_rand($userIds)],
                'created_at' => $now->copy()->subDays(rand(1, 120)),
                'updated_at' => $now,
            ];
        }

        // 5) إدخال/تحديث آمن: slug unique => upsert
        // هذا يمنع تكرار الإدخالات عند تشغيل seeder أكثر من مرة
        DB::table('campaigns')->upsert(
            $rows,
            ['slug'], // unique key
            [
                'title_ar',
                'title_en',
                'description_ar',
                'description_en',
                'goal_amount',
                'current_amount',
                'currency',
                'status',
                'is_featured',
                'priority',
                'cover_image_path',
                'starts_at',
                'ends_at',
                'created_by',
                'updated_at'
            ]
        );
    }

    /**
     * توليد current_amount منطقياً بناءً على status.
     */
    private function makeCurrentAmount(float $goal, string $status): float
    {
        // current_amount لازم لا يتجاوز goal_amount (غالباً)
        // وبحسب الحالة:
        // - draft: غالباً 0
        // - active: 5% إلى 75%
        // - paused: 10% إلى 60%
        // - ended: 80% إلى 110% (أحياناً تتجاوز الهدف)
        // - archived: أي شيء، لكن غالباً مكتمل/قديم
        $ratio = match ($status) {
            'draft' => 0.0,
            'active' => rand(5, 75) / 100,
            'paused' => rand(10, 60) / 100,
            'ended' => rand(80, 110) / 100,
            'archived' => rand(40, 120) / 100,
            default => rand(0, 50) / 100,
        };

        $current = $goal * $ratio;

        // تقريب إلى خانتين عشريتين
        return round($current, 2);
    }

    /**
     * إنشاء starts_at / ends_at حسب الحالة.
     */
    private function makeDates(Carbon $now, string $status): array
    {
        $startsAt = null;
        $endsAt = null;

        if (in_array($status, ['active', 'paused', 'ended', 'archived'], true)) {
            // تبدأ قبل الآن بـ 10 إلى 90 يوم
            $startsAt = $now->copy()->subDays(rand(10, 90))->setTime(rand(8, 14), rand(0, 59));
        }

        if ($status === 'active') {
            // تنتهي بعد الآن بـ 7 إلى 60 يوم
            $endsAt = $now->copy()->addDays(rand(7, 60))->setTime(rand(16, 21), rand(0, 59));
        } elseif ($status === 'paused') {
            // paused قد يكون له end قريب أو null
            $endsAt = (rand(0, 1) === 1)
                ? $now->copy()->addDays(rand(7, 45))->setTime(rand(16, 21), rand(0, 59))
                : null;
        } elseif (in_array($status, ['ended', 'archived'], true)) {
            // انتهت قبل الآن بـ 1 إلى 30 يوم (أو أقدم للأرشيف)
            $daysAgo = $status === 'archived' ? rand(30, 180) : rand(1, 30);
            $endsAt = $now->copy()->subDays($daysAgo)->setTime(rand(16, 21), rand(0, 59));

            // ضمان end بعد start
            if ($startsAt && $endsAt->lessThanOrEqualTo($startsAt)) {
                $endsAt = $startsAt->copy()->addDays(rand(7, 40));
            }
        }

        return [$startsAt, $endsAt];
    }

    /**
     * ضمان slug فريد عبر فحص الجدول + إضافة لاحقة رقمية.
     */
    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 2;

        while (DB::table('campaigns')->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
