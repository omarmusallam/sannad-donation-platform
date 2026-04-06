<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Report;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $status   = $request->get('status'); // active|paused|null
        $featured = $request->get('featured'); // 1|null
        $sort     = $request->get('sort', 'featured'); // featured|new|progress|goal

        $campaigns = Campaign::query()
            ->whereIn('status', ['active', 'paused'])
            ->when($status && in_array($status, ['active', 'paused'], true), fn($qq) => $qq->where('status', $status))
            ->when($featured === '1', fn($qq) => $qq->where('is_featured', true))
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('title_ar', 'like', "%{$q}%")
                        ->orWhere('title_en', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->withCount(['donations as donors_count' => fn($qq) => $qq->where('status', 'paid')])
            ->when($sort === 'new', fn($qq) => $qq->latest())
            ->when($sort === 'goal', fn($qq) => $qq->orderByDesc('goal_amount'))
            ->when($sort === 'progress', fn($qq) => $qq->orderByRaw('(CASE WHEN goal_amount > 0 THEN (current_amount/goal_amount) ELSE 0 END) DESC'))
            ->when($sort === 'featured', function ($qq) {
                $qq->orderByDesc('is_featured')
                    ->orderByDesc('priority')
                    ->latest();
            })
            ->paginate(12)
            ->withQueryString();

        return view('public.campaigns.index', compact('campaigns', 'q', 'status', 'featured', 'sort'));
    }

    public function show(string $slug)
    {
        $campaign = Campaign::query()
            ->where('slug', $slug)
            ->whereIn('status', ['active', 'paused'])
            ->firstOrFail();

        $donorsCount = $campaign->donations()
            ->where('status', 'paid')
            ->count();

        $latestDonations = $campaign->donations()
            ->where('status', 'paid')
            ->latest()
            ->limit(8)
            ->get(['id', 'donor_name', 'is_anonymous', 'amount', 'currency', 'created_at']);

        $updates = $campaign->updates()
            ->where('is_public', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $reports = Report::query()
            ->where('is_public', true)
            ->where('campaign_id', $campaign->id)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->latest()
            ->limit(8)
            ->get(['id', 'title_ar', 'title_en', 'period_month', 'period_year', 'pdf_path', 'created_at']);

        return view('public.campaigns.show', compact(
            'campaign',
            'donorsCount',
            'latestDonations',
            'updates',
            'reports'
        ));
    }
}
