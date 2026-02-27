<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.view')->only(['index']);
        $this->middleware('permission:reports.create')->only(['create', 'store']);
        $this->middleware('permission:reports.edit')->only(['edit', 'update']);
        $this->middleware('permission:reports.delete')->only(['destroy']);
    }

    public function index()
    {
        $reports = Report::with('campaign')
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function create()
    {
        $campaigns = Campaign::query()
            ->orderByDesc('is_featured')
            ->orderByDesc('priority')
            ->latest()
            ->get(['id', 'title_ar', 'title_en']);

        return view('admin.reports.create', compact('campaigns'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request, update: false);

        $data['created_by'] = auth()->id();

        // checkbox normalize (مهم جدًا)
        $data['is_public'] = $request->boolean('is_public');

        DB::transaction(function () use ($request, &$data) {
            // PDF required on create
            $data['pdf_path'] = $request->file('pdf')->store('reports', 'public');

            Report::create($data);
        });

        return redirect()
            ->route('admin.reports.index')
            ->with('success', 'تم إضافة التقرير بنجاح');
    }

    public function edit(Report $report)
    {
        $campaigns = Campaign::query()
            ->orderByDesc('is_featured')
            ->orderByDesc('priority')
            ->latest()
            ->get(['id', 'title_ar', 'title_en']);

        return view('admin.reports.edit', compact('report', 'campaigns'));
    }

    public function update(Request $request, Report $report)
    {
        $data = $this->validateData($request, update: true);

        $data['is_public'] = $request->boolean('is_public');

        DB::transaction(function () use ($request, $report, &$data) {
            if ($request->hasFile('pdf')) {
                // خزّن الجديد أولًا
                $newPath = $request->file('pdf')->store('reports', 'public');
                $oldPath = $report->pdf_path;

                $data['pdf_path'] = $newPath;

                $report->update($data);

                // احذف القديم بعد نجاح التحديث
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }

                return;
            }

            $report->update($data);
        });

        return redirect()
            ->route('admin.reports.index')
            ->with('success', 'تم تحديث التقرير بنجاح');
    }

    public function destroy(Report $report)
    {
        DB::transaction(function () use ($report) {
            $path = $report->pdf_path;

            $report->delete();

            if ($path) {
                Storage::disk('public')->delete($path);
            }
        });

        return back()->with('success', 'تم حذف التقرير');
    }

    private function validateData(Request $request, bool $update = false): array
    {
        return $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],

            'summary_ar' => ['nullable', 'string'],
            'summary_en' => ['nullable', 'string'],

            'period_month' => ['nullable', 'string', 'max:2'],
            'period_year'  => ['nullable', 'string', 'max:4'],

            'campaign_id' => ['nullable', 'exists:campaigns,id'],

            'is_public' => ['nullable', 'boolean'],

            'pdf' => $update
                ? ['nullable', 'mimes:pdf', 'max:10240']   // 10MB
                : ['required', 'mimes:pdf', 'max:10240'],
        ]);
    }
}
