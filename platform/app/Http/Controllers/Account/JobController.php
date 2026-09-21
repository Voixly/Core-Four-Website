<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobDocument;
use App\Models\JobQuote;
use App\Services\JobOpsService;
use App\Services\JobService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = Job::query()
            ->with(['pipeline', 'stage', 'documentRequests'])
            ->whereHas('contacts', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->get();

        return view('account.home', compact('jobs'));
    }

    public function show(Request $request, Job $job, JobService $jobs): View
    {
        abort_unless($jobs->customerCanAccess($job, $request->user()), 403);

        $job->load([
            'pipeline.stages', 'stage', 'documentRequests.fulfillment',
            'documents' => fn ($q) => $q->where('visibility', 'customer'),
            'events' => fn ($q) => $q->where('customer_visible', true)->latest(),
            'appointments' => fn ($q) => $q->where('status', 'scheduled')->where('starts_at', '>=', now()->subDay())->orderBy('starts_at'),
            'quotes' => fn ($q) => $q->whereIn('status', ['sent', 'approved'])->with('items'),
            'warranties',
        ]);

        return view('account.job', compact('job'));
    }

    public function acceptQuote(Request $request, Job $job, JobQuote $quote, JobService $jobs, JobOpsService $ops): RedirectResponse
    {
        abort_unless($jobs->customerCanAccess($job, $request->user()), 403);
        abort_unless($quote->job_id === $job->id, 404);
        abort_unless($quote->status === 'sent', 422);

        $ops->setQuoteStatus($quote, 'approved', $request->user());

        return back()->with('success', 'Estimate approved. The Tomball office will schedule the work.');
    }

    public function upload(Request $request, Job $job, JobService $jobs): RedirectResponse
    {
        abort_unless($jobs->customerCanAccess($job, $request->user()), 403);

        $data = $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,heic,webp'],
            'request_id' => ['nullable', 'exists:job_document_requests,id'],
        ]);
        $data['visibility'] = 'customer';
        $data['category'] = 'other';

        $jobs->storeDocument($job, $request->file('file'), $request->user(), $data);

        return back()->with('success', 'File uploaded. The Tomball office can see it now.');
    }

    public function download(Request $request, Job $job, JobDocument $document, JobService $jobs): StreamedResponse
    {
        abort_unless($jobs->customerCanAccess($job, $request->user()), 403);
        abort_unless($document->job_id === $job->id && $document->visibility === 'customer', 404);

        return Storage::disk('documents')->download($document->path, $document->original_name);
    }
}
