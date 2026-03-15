<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\FinanceReports\FinanceReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinanceReportController extends Controller
{
    public function __construct(private FinanceReportService $service)
    {
        $this->middleware('permission:finance_reports.view')->only([
            'index',
            'monthly',
            'campaign',
            'gateway',
            'currency',
            'status',
            'paymentMethod',
        ]);
    }

    public function index()
    {
        return view('admin.finance-reports.index');
    }

    private function range(Request $request): array
    {
        Validator::make($request->all(), [
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date'],
        ])->validate();

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    public function monthly(Request $request)
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $year = (int) ($validated['year'] ?? now()->year);

        $rows = $this->service->byMonth($year);

        return view('admin.finance-reports.monthly', compact('year', 'rows'));
    }

    public function campaign(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => ['nullable', 'integer', 'exists:campaigns,id'],
        ]);

        [$from, $to] = $this->range($request);

        $campaignId = !empty($validated['campaign_id']) ? (int) $validated['campaign_id'] : null;

        $kpis = $this->service->kpis($from, $to, $campaignId);
        $rows = $campaignId ? null : $this->service->byCampaign($from, $to);

        $campaigns = Campaign::query()
            ->select('id', 'title_ar', 'title_en')
            ->orderByDesc('id')
            ->get();

        return view('admin.finance-reports.campaign', compact(
            'from',
            'to',
            'campaignId',
            'kpis',
            'rows',
            'campaigns'
        ));
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
