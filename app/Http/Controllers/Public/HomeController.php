<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Report;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    public function index()
    {
        $driver = DB::connection()->getDriverName();
        $anonymousKeyExpression = $driver === 'sqlite'
            ? "COUNT(DISTINCT COALESCE(NULLIF(donor_email,''), 'anon#' || id)) as c"
            : "COUNT(DISTINCT COALESCE(NULLIF(donor_email,''), CONCAT('anon#', id))) as c";

        // ===== KPIs =====
        $paidAgg = Donation::query()
            ->where('status', 'paid')
            ->selectRaw('COALESCE(SUM(amount),0) as total_paid')
            ->selectRaw('COUNT(*) as paid_count')
            ->first();

        $totalPaid = (float) ($paidAgg->total_paid ?? 0);
        $paidCount = (int) ($paidAgg->paid_count ?? 0);

        $donorsCount = (int) Donation::query()
            ->where('status', 'paid')
            ->selectRaw($anonymousKeyExpression)
            ->value('c');

        $avgDonation = $paidCount > 0 ? round($totalPaid / $paidCount, 2) : 0;

        $activeCampaigns = (int) Campaign::query()
            ->whereIn('status', ['active', 'paused'])
            ->count();

        // ===== Featured campaigns =====
        $featuredCampaigns = Campaign::query()
            ->whereIn('status', ['active', 'paused'])
            ->orderByDesc('is_featured')
            ->orderByDesc('priority')
            ->latest()
            ->limit(4)
            ->get([
                'id',
                'title_ar',
                'title_en',
                'slug',
                'description_ar',
                'description_en',
                'goal_amount',
                'current_amount',
                'currency',
                'is_featured',
                'cover_image_path',
            ]);

        // ===== Latest public reports =====
        $latestReports = Report::query()
            ->public()
            ->with('campaign:id,title_ar,title_en,slug')
            ->latestPeriod()
            ->limit(3)
            ->get([
                'id',
                'title_ar',
                'title_en',
                'summary_ar',
                'summary_en',
                'period_month',
                'period_year',
                'campaign_id',
                'pdf_path'
            ]);

        return view('public.home', compact(
            'totalPaid',
            'paidCount',
            'avgDonation',
            'donorsCount',
            'activeCampaigns',
            'featuredCampaigns',
            'latestReports'
        ));
    }
}
