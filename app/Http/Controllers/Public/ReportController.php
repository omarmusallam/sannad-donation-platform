<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::query()
            ->where('is_public', true)
            ->with('campaign')
            ->latest()
            ->paginate(12);

        return view('public.transparency.reports', compact('reports'));
    }

    public function show(Report $report)
    {
        abort_unless($report->is_public, 404);

        $report->load('campaign');

        return view('public.transparency.report_show', compact('report'));
    }
}
