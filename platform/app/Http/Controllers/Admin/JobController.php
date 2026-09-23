<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobDocument;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use App\Services\JobService;
use Database\Seeders\PipelineSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $pipelines = $this->pipelines();
        $pipeline = $request->pipeline
            ? $pipelines->firstWhere('slug', $request->pipeline)
            : $pipelines->first();

        if (! $pipeline) {
            return view('admin.jobs.index', [
                'pipelines' => $pipelines,
                'pipeline' => null,
                'jobsByStage' => collect(),
            ]);
        }

        $jobs = Job::query()
            ->with(['lead', 'assignee', 'stage', 'documentRequests'])
            ->where('pipeline_id', $pipeline->id)
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->q, function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('number', 'like', '%'.$term.'%')
                        ->orWhere('city', 'like', '%'.$term.'%')
                        ->orWhere('address', 'like', '%'.$term.'%')
                        ->orWhereHas('lead', fn ($lead) => $lead->where('name', 'like', '%'.$term.'%'));
                });
            })
            ->latest()
            ->get()
            ->groupBy('stage_id');

        return view('admin.jobs.index', [
            'pipelines' => $pipelines,
            'pipeline' => $pipeline,
            'jobsByStage' => $jobs,
        ]);
    }

    public function create()
    {
        return view('admin.jobs.create', [
            'pipelines' => $this->pipelines(),
            'staff' => User::staff()->get(),
            'leads' => Lead::query()->whereNull('job_id')->latest()->limit(80)->get(),
        ]);
    }

    private function pipelines()
    {
        $pipelines = Pipeline::query()->where('is_active', true)->with('stages')->orderBy('sort')->get();

        if ($pipelines->isNotEmpty()) {
            return $pipelines;
        }

        try {
            (new PipelineSeeder)->run();
        } catch (\Throwable) {
            return $pipelines;
        }

        return Pipeline::query()->where('is_active', true)->with('stages')->orderBy('sort')->get();
    }

    public function storeStandalone(Request $request, JobService $jobs): RedirectResponse
    {
        $data = $request->validate([
            'pipeline_id' => ['required', 'exists:pipelines,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'type' => ['nullable', 'in:residential,commercial'],
            'address' => ['nullable', 'string', 'max:190'],
            'city' => ['nullable', 'string', 'max:80'],
            'zip' => ['nullable', 'string', 'max:16'],
            'payment_path' => ['required', 'in:'.implode(',', Job::PAYMENT_PATHS)],
            'urgency' => ['required', 'in:standard,emergency'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'customer_summary' => ['nullable', 'string', 'max:2000'],
            'roof_type' => ['nullable', 'in:'.implode(',', Job::ROOF_TYPES)],
            'squares' => ['nullable', 'numeric', 'min:0'],
        ]);

        $job = $jobs->createStandalone($data, $request->user());

        return redirect()->route('admin.jobs.show', $job)->with('success', 'Job '.$job->number.' is open.');
    }

    public function show(Job $job)
    {
        $job->load([
            'lead', 'pipeline.stages', 'stage', 'assignee',
            'contacts.user', 'events.user', 'documentRequests.fulfillment',
            'documents.user', 'invites',
            'appointments.assignee', 'quotes.items', 'changeOrders',
            'invoices', 'materials', 'tasks.assignee', 'warranties', 'costLines',
        ]);

        return view('admin.jobs.show', [
            'job' => $job,
            'staff' => User::staff()->get(),
            'tab' => request('tab', 'site'),
        ]);
    }

    public function store(Request $request, Lead $lead, JobService $jobs): RedirectResponse
    {
        $data = $request->validate([
            'pipeline_id' => ['required', 'exists:pipelines,id'],
            'address' => ['nullable', 'string', 'max:190'],
            'city' => ['nullable', 'string', 'max:80'],
            'zip' => ['nullable', 'string', 'max:16'],
            'payment_path' => ['required', 'in:'.implode(',', Job::PAYMENT_PATHS)],
            'urgency' => ['required', 'in:standard,emergency'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'customer_summary' => ['nullable', 'string', 'max:2000'],
        ]);

        $job = $jobs->createFromLead($lead, $data, $request->user());

        return redirect()->route('admin.jobs.show', $job)->with('success', 'Job '.$job->number.' is open.');
    }

    public function update(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
            'scheduled_at' => ['nullable', 'date'],
            'customer_summary' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:open,on_hold,won,lost'],
            'address' => ['nullable', 'string', 'max:190'],
            'city' => ['nullable', 'string', 'max:80'],
            'zip' => ['nullable', 'string', 'max:16'],
            'payment_path' => ['nullable', 'in:'.implode(',', Job::PAYMENT_PATHS)],
            'urgency' => ['nullable', 'in:standard,emergency'],
            'roof_type' => ['nullable', 'in:'.implode(',', Job::ROOF_TYPES)],
            'squares' => ['nullable', 'numeric', 'min:0'],
            'stories' => ['nullable', 'integer', 'min:1', 'max:8'],
            'pitch' => ['nullable', 'string', 'max:24'],
            'material_system' => ['nullable', 'string', 'max:80'],
            'insurance_carrier' => ['nullable', 'string', 'max:120'],
            'claim_number' => ['nullable', 'string', 'max:80'],
            'hoa_name' => ['nullable', 'string', 'max:120'],
            'access_notes' => ['nullable', 'string', 'max:2000'],
            'crew_name' => ['nullable', 'string', 'max:120'],
        ]);

        $job->update($data);
        $job->log($request->user(), 'updated', 'Job details saved');

        return back()->with('success', 'Job updated.');
    }

    public function move(Request $request, Job $job, JobService $jobs): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'stage_id' => ['required', 'exists:pipeline_stages,id'],
        ]);

        $stage = PipelineStage::query()->findOrFail($data['stage_id']);

        try {
            $jobs->move($job, $stage, $request->user());
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->withErrors($e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'stage_id' => $stage->id,
                'stage' => $stage->name,
                'status' => $job->fresh()->status,
            ]);
        }

        return back()->with('success', 'Moved to '.$stage->name.'.');
    }

    public function invite(Request $request, Job $job, JobService $jobs): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'contact_role' => ['nullable', 'in:homeowner,pm,other'],
        ]);

        $jobs->invite($job, $data, $request->user());

        return back()->with('success', 'Invite sent to '.$data['email'].'.');
    }

    public function requestDocument(Request $request, Job $job, JobService $jobs): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:40'],
            'required' => ['nullable', 'boolean'],
        ]);

        $jobs->addRequest($job, $data, $request->user());

        return back()->with('success', 'Document requested.');
    }

    public function upload(Request $request, Job $job, JobService $jobs): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,heic,webp'],
            'request_id' => ['nullable', 'exists:job_document_requests,id'],
            'category' => ['nullable', 'string', 'max:40'],
            'visibility' => ['nullable', 'in:staff,customer'],
        ]);

        $jobs->storeDocument($job, $request->file('file'), $request->user(), $data);

        return back()->with('success', 'File uploaded.');
    }

    public function download(Job $job, JobDocument $document): StreamedResponse
    {
        abort_unless($document->job_id === $job->id, 404);

        return Storage::disk('documents')->download($document->path, $document->original_name);
    }
}
