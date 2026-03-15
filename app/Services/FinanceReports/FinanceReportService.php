<?php

namespace App\Services\FinanceReports;

use App\Models\Donation;
use Illuminate\Support\Facades\DB;

class FinanceReportService
{
    public function kpis($from, $to, ?int $campaignId = null): array
    {
        $q = Donation::query()
            ->paid()
            ->paidDateBetween($from, $to);

        if ($campaignId) {
            $q->where('campaign_id', $campaignId);
        }

        return [
            'total_amount' => (float) $q->sum('amount'),
            'donations_count' => (int) $q->count(),
            'avg_donation' => (float) $q->avg('amount'),
            'unique_donors' => (int) $q
                ->whereNotNull('donor_email')
                ->distinct('donor_email')
                ->count('donor_email'),
        ];
    }

    public function byMonth(int $year)
    {
        return Donation::query()
            ->paid()
            ->whereYear(DB::raw('COALESCE(paid_at, created_at)'), $year)
            ->selectRaw("LPAD(MONTH(COALESCE(paid_at, created_at)), 2, '0') as month")
            ->selectRaw("COUNT(*) as donations_count")
            ->selectRaw("SUM(amount) as total_amount")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function byCampaign($from, $to)
    {
        return Donation::query()
            ->paid()
            ->paidDateBetween($from, $to)
            ->selectRaw('campaign_id, COUNT(*) as donations_count, SUM(amount) as total_amount')
            ->with('campaign:id,title_ar,title_en,currency')
            ->groupBy('campaign_id')
            ->orderByDesc('total_amount')
            ->get();
    }

    public function byGateway($from, $to)
    {
        return Donation::query()
            ->paid()
            ->paidDateBetween($from, $to)
            ->selectRaw("COALESCE(NULLIF(provider, ''), 'N/A') as provider")
            ->selectRaw('COUNT(*) as donations_count, SUM(amount) as total_amount')
            ->groupBy('provider')
            ->orderByDesc('total_amount')
            ->get();
    }

    public function byCurrency($from, $to)
    {
        return Donation::query()
            ->paid()
            ->paidDateBetween($from, $to)
            ->selectRaw("COALESCE(NULLIF(currency, ''), 'N/A') as currency")
            ->selectRaw('COUNT(*) as donations_count, SUM(amount) as total_amount')
            ->groupBy('currency')
            ->orderByDesc('total_amount')
            ->get();
    }

    public function byStatus($from, $to)
    {
        return Donation::query()
            ->paidDateBetween($from, $to)
            ->selectRaw("COALESCE(NULLIF(status, ''), 'N/A') as status")
            ->selectRaw('COUNT(*) as donations_count, SUM(amount) as total_amount')
            ->groupBy('status')
            ->orderByDesc('donations_count')
            ->get();
    }

    public function byPaymentMethod($from, $to)
    {
        return Donation::query()
            ->paidDateBetween($from, $to)
            ->selectRaw("COALESCE(NULLIF(payment_method, ''), 'N/A') as payment_method")
            ->selectRaw('COUNT(*) as donations_count, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->orderByDesc('donations_count')
            ->get();
    }
}
