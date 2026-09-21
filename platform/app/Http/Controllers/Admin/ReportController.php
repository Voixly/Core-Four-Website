<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Support\PerformanceReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = Report::query()
            ->whereNotIn('slug', ['residential-ads', 'review-shield'])
            ->orderBy('title')
            ->get();

        return view('admin.reports.index', compact('reports'));
    }

    public function show(string $slug): View|RedirectResponse
    {
        if ($slug === 'residential-ads') {
            return redirect()->route('admin.ads');
        }

        if ($slug === 'review-shield') {
            return redirect()->route('admin.reviews.index');
        }

        $report = Report::query()->where('slug', $slug)->firstOrFail();
        $view = 'admin.reports.'.$report->slug;
        if (! view()->exists($view)) {
            $view = 'admin.reports.show';
        }

        $perf = $slug === 'performance' ? PerformanceReport::make() : null;

        return view($view, compact('report', 'perf'));
    }

    public function pdf(string $slug): Response|StreamedResponse
    {
        abort_unless($slug === 'performance', 404);

        $report = Report::query()->where('slug', $slug)->firstOrFail();
        $perf = PerformanceReport::make();

        return Pdf::loadView('admin.reports.performance-pdf', compact('report', 'perf'))
            ->setPaper('letter')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ])
            ->download($perf['meta']['filename']);
    }
}
