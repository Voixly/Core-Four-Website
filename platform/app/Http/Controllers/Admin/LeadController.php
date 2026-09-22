<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\User;
use App\Services\JobService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $leads = Lead::query()
            ->with('assignee')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->source, fn ($q, $source) => $q->where('source', $source))
            ->when($request->q, function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.leads.index', compact('leads'));
    }

    public function show(Lead $lead, JobService $jobs): View
    {
        $lead->load(['events.user', 'assignee', 'job']);
        $staff = User::staff()->get();
        $pipelines = Pipeline::query()->where('is_active', true)->orderBy('sort')->get();
        $suggested = $jobs->suggestPipeline($lead);

        return view('admin.leads.show', compact('lead', 'staff', 'pipelines', 'suggested'));
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['nullable', 'in:'.implode(',', Lead::STATUSES)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $changes = [];
        foreach ($data as $key => $value) {
            if ((string) $lead->{$key} !== (string) $value) {
                $changes[] = $key.' → '.($value ?: 'none');
            }
        }

        $lead->update($data);

        if ($changes) {
            $lead->log($request->user(), 'updated', implode(', ', $changes));
        }

        return back()->with('success', 'Lead updated.');
    }

    public function note(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:4000']]);
        $lead->log($request->user(), 'note', $data['body']);

        return back()->with('success', 'Note added.');
    }
}
