<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampaignUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:campaign_updates.view')->only(['index']);
        $this->middleware('permission:campaign_updates.create')->only(['create', 'store']);
        $this->middleware('permission:campaign_updates.edit')->only(['edit', 'update']);
        $this->middleware('permission:campaign_updates.delete')->only(['destroy']);
    }

    public function index(Campaign $campaign)
    {
        $updates = $campaign->updates()
            ->latest()
            ->paginate(20);

        return view('admin.campaigns.updates.index', compact('campaign', 'updates'));
    }

    public function create(Campaign $campaign)
    {
        return view('admin.campaigns.updates.create', compact('campaign'));
    }

    public function store(Request $request, Campaign $campaign)
    {
        $data = $this->validateData($request);

        $data['created_by'] = auth()->id();
        $data['is_public'] = $request->boolean('is_public');

        // إذا ما انبعت تاريخ نشر: انشر الآن
        $data['published_at'] = $data['published_at'] ?? now();

        DB::transaction(function () use ($campaign, $data) {
            $campaign->updates()->create($data);
        });

        return redirect()
            ->route('admin.campaigns.updates.index', $campaign)
            ->with('success', 'تم إضافة التحديث بنجاح');
    }

    public function edit(Campaign $campaign, CampaignUpdate $update)
    {
        $update = $campaign->updates()->whereKey($update->getKey())->firstOrFail();

        return view('admin.campaigns.updates.edit', compact('campaign', 'update'));
    }

    public function update(Request $request, Campaign $campaign, CampaignUpdate $update)
    {
        $update = $campaign->updates()->whereKey($update->getKey())->firstOrFail();

        $data = $this->validateData($request);
        $data['is_public'] = $request->boolean('is_public');

        DB::transaction(function () use ($update, $data) {
            $update->update($data);
        });

        return redirect()
            ->route('admin.campaigns.updates.index', $campaign)
            ->with('success', 'تم تحديث التحديث بنجاح');
    }

    public function destroy(Campaign $campaign, CampaignUpdate $update)
    {
        $update = $campaign->updates()->whereKey($update->getKey())->firstOrFail();

        DB::transaction(function () use ($update) {
            $update->delete();
        });

        return back()->with('success', 'تم حذف التحديث');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],

            'body_ar' => ['nullable', 'string'],
            'body_en' => ['nullable', 'string'],

            'is_public' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);
    }
}
