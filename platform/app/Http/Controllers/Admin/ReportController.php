<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = Report::query()->orderBy('title')->get();

        return view('admin.reports.index', compact('reports'));
    }

    public function show(string $slug): View
    {
        $report = Report::query()->where('slug', $slug)->firstOrFail();
        $view = 'admin.reports.'.$report->slug;
        if (! view()->exists($view)) {
            $view = 'admin.reports.show';
        }

        return view($view, compact('report'));
    }
}
