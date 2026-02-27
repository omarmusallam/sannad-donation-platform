<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function __construct()
    {
        // Dashboard
        $this->middleware('permission:dashboard.view')->only(['dashboard']);

        // Campaigns
        $this->middleware('permission:campaigns.view')->only(['index']);
        $this->middleware('permission:campaigns.create')->only(['create', 'store']);
        $this->middleware('permission:campaigns.edit')->only(['edit', 'update']);
        $this->middleware('permission:campaigns.delete')->only(['destroy']);
    }

    public function dashboard()
    {
        $totalDonations   = Donation::count();
        $totalPaid        = (float) Donation::where('status', 'paid')->sum('amount');
        $activeCampaigns  = Campaign::where('status', 'active')->count();

        $topCampaign = Campaign::withSum(
            ['donations as total_collected' => fn($q) => $q->where('status', 'paid')],
            'amount'
        )->orderByDesc('total_collected')->first();

        $latestDonations = Donation::with('campaign')
            ->latest()
            ->limit(8)
            ->get();

        $todayPaid = (float) Donation::where('status', 'paid')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        $monthPaid = (float) Donation::where('status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');

        $statusCountsRaw = Donation::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        $statusCounts = [
            'paid'    => (int) ($statusCountsRaw['paid'] ?? 0),
            'pending' => (int) ($statusCountsRaw['pending'] ?? 0),
            'failed'  => (int) ($statusCountsRaw['failed'] ?? 0),
        ];

        // آخر 14 يوم (مدفوع)
        $from = Carbon::today()->subDays(13);

        $daily = Donation::where('status', 'paid')
            ->whereDate('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as d, SUM(amount) as s')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $labels = [];
        $values = [];
        for ($i = 0; $i < 14; $i++) {
            $day = $from->copy()->addDays($i)->toDateString();
            $labels[] = $day;
            $values[] = (float) ($daily[$day]->s ?? 0);
        }

        $dailySeries = [
            'labels' => $labels,
            'values' => $values,
        ];

        $statusSeries = [
            'labels' => ['paid', 'pending', 'failed'],
            'values' => [$statusCounts['paid'], $statusCounts['pending'], $statusCounts['failed']],
        ];

        // Top 5 campaigns paid
        $topCampaigns = Campaign::withSum(
            ['donations as total_collected' => fn($q) => $q->where('status', 'paid')],
            'amount'
        )->orderByDesc('total_collected')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalDonations',
            'totalPaid',
            'activeCampaigns',
            'topCampaign',
            'latestDonations',
            'todayPaid',
            'monthPaid',
            'statusCounts',
            'dailySeries',
            'statusSeries',
            'topCampaigns'
        ));
    }

    public function index()
    {
        $campaigns = Campaign::latest()->paginate(15);
        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('admin.campaigns.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['is_featured'] = $request->boolean('is_featured');

        // ضمان وجود slug دائمًا
        $titleForSlug = $data['title_en'] ?: $data['title_ar'];
        $data['slug'] = Campaign::makeSlug($titleForSlug);

        $data['created_by'] = auth()->id();

        DB::transaction(function () use ($request, &$data) {
            if ($request->hasFile('cover_image')) {
                $data['cover_image_path'] = $request->file('cover_image')
                    ->store('campaigns', 'public');
            }

            Campaign::create($data);
        });

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', 'تم إنشاء الحملة بنجاح');
    }

    public function edit(Campaign $campaign)
    {
        return view('admin.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $data = $this->validateData($request);

        // checkbox: لو ما انبعثت لازم تتحول false
        $data['is_featured'] = $request->boolean('is_featured');

        DB::transaction(function () use ($request, $campaign, &$data) {
            if ($request->hasFile('cover_image')) {
                // خزّن الجديدة أولًا (أمانًا)
                $newPath = $request->file('cover_image')->store('campaigns', 'public');
                $oldPath = $campaign->cover_image_path;

                $data['cover_image_path'] = $newPath;

                $campaign->update($data);

                // احذف القديمة بعد نجاح التحديث
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }

                return;
            }

            $campaign->update($data);
        });

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', 'تم تحديث الحملة بنجاح');
    }

    public function destroy(Campaign $campaign)
    {
        DB::transaction(function () use ($campaign) {
            $path = $campaign->cover_image_path;

            $campaign->delete();

            // حذف الصورة بعد حذف السجل (أحيانًا أفضل للفصل بين البيانات والملفات)
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        });

        return back()->with('success', 'تم حذف الحملة');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title_ar'        => 'required|string|max:255',
            'title_en'        => 'nullable|string|max:255',
            'description_ar'  => 'nullable|string',
            'description_en'  => 'nullable|string',
            'goal_amount'     => 'required|numeric|min:0',
            'currency'        => 'required|string|max:3',
            'status'          => 'required|in:draft,active,paused,ended,archived',
            'is_featured'     => 'nullable|boolean',
            'priority'        => 'nullable|integer|min:0',
            'cover_image'     => 'nullable|image|max:2048',
        ]);
    }
}
