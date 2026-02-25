<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\FinanceReports\FinanceReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceReportController extends Controller
{
    public function __construct(private FinanceReportService $service) {}

    public function index()
    {
        return view('admin.finance-reports.index');
    }

    private function range(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::parse($request->to)->endOfDay()     : now()->endOfDay();
        return [$from, $to];
    }

    public function monthly(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);
        $rows = $this->service->byMonth($year);

        return view('admin.finance-reports.monthly', compact('year', 'rows'));
    }

    public function campaign(Request $request)
    {
        [$from, $to] = $this->range($request);

        $campaignId = $request->campaign_id ? (int) $request->campaign_id : null;
        $kpis = $this->service->kpis($from, $to, $campaignId);

        $rows = $campaignId ? null : $this->service->byCampaign($from, $to);

        $campaigns = Campaign::query()
            ->select('id', 'title_ar', 'title_en')
            ->orderByDesc('id')
            ->get();

        return view('admin.finance-reports.campaign', compact('from', 'to', 'campaignId', 'kpis', 'rows', 'campaigns'));
    }

    public function gateway(Request $request)
    {
        [$from, $to] = $this->range($request);
        $kpis = $this->service->kpis($from, $to);
        $rows = $this->service->byGateway($from, $to);

        return view('admin.finance-reports.gateway', compact('from', 'to', 'kpis', 'rows'));
    }

    public function currency(Request $request)
    {
        [$from, $to] = $this->range($request);
        $kpis = $this->service->kpis($from, $to);
        $rows = $this->service->byCurrency($from, $to);

        return view('admin.finance-reports.currency', compact('from', 'to', 'kpis', 'rows'));
    }

    public function status(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rows = $this->service->byStatus($from, $to);

        return view('admin.finance-reports.status', compact('from', 'to', 'rows'));
    }

    public function paymentMethod(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rows = $this->service->byPaymentMethod($from, $to);

        return view('admin.finance-reports.payment-method', compact('from', 'to', 'rows'));
    }
}
