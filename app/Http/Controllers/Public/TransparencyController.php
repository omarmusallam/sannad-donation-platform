<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Report;

class TransparencyController extends Controller
{
    public function index()
    {
        // ✅ إجمالي المدفوع
        $totalPaid = (float) Donation::query()->where('status', 'paid')->sum('amount');

        // ✅ عدد التبرعات المدفوعة
        $paidDonationsCount = Donation::query()->where('status', 'paid')->count();

        // ✅ عدد المتبرعين (أفضل من email فقط):
        // لو عنده email نعتبره identity، وإلا نستخدم donation id كمميز (حتى لا ينقص العد)
        $donorsCount = (int) Donation::query()
            ->where('status', 'paid')
            ->selectRaw("COUNT(DISTINCT COALESCE(NULLIF(donor_email,''), CONCAT('anon#', id))) as c")
            ->value('c');

        // ✅ الحملات النشطة
        $activeCampaigns = (int) Campaign::query()->whereIn('status', ['active', 'paused'])->count();

        // ✅ أعلى الحملات بناء على مجموع المدفوع (الأدق)
        $topCampaigns = Campaign::query()
            ->whereIn('status', ['active', 'paused'])
            ->withSum(['donations as paid_total' => fn($q) => $q->where('status', 'paid')], 'amount')
            ->orderByDesc('paid_total')
            ->orderByDesc('is_featured')
            ->orderByDesc('priority')
            ->limit(5)
            ->get(['title_ar', 'title_en', 'slug', 'goal_amount', 'currency', 'is_featured', 'status']);

        // ✅ أحدث التقارير العامة
        $latestReports = Report::query()
            ->where('is_public', true)
            ->with('campaign:id,title_ar,title_en,slug')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->latest()
            ->limit(5)
            ->get();

        // ✅ أحدث تبرعات (لواجهة الشفافية)
        $latestDonations = Donation::query()
            ->where('status', 'paid')
            ->latest()
            ->limit(6)
            ->get(['id', 'donor_name', 'donor_email', 'is_anonymous', 'amount', 'currency', 'created_at']);

        return view('public.transparency.index', compact(
            'totalPaid',
            'paidDonationsCount',
            'donorsCount',
            'activeCampaigns',
            'topCampaigns',
            'latestReports',
            'latestDonations'
        ));
    }
}
