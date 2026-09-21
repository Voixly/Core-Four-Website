<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdsPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdsController extends Controller
{
    public function index(): View
    {
        $ads = AdsPlan::make();

        return view('admin.ads.index', compact('ads'));
    }

    public function pdf(): Response|StreamedResponse
    {
        $ads = AdsPlan::make();

        return Pdf::loadView('admin.ads.pdf', compact('ads'))
            ->setPaper('letter')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ])
            ->download($ads['meta']['filename']);
    }
}
