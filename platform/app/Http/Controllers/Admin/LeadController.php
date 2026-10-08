<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\User;
use App\Services\JobNimbusService;
use App\Services\JobService;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $leads = Lead::query()
            ->exceptProspects()
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

        $staff = User::staff()->orderBy('name')->get();

        return view('admin.leads.index', compact('leads', 'staff'));
    }

    public function prospects(Request $request): View
    {
        $prospects = Lead::query()
            ->with('assignee')
            ->where('source', 'prospect')
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
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

        return view('admin.prospects.index', compact('prospects'));
    }

    public function respond(Request $request, Lead $lead, LeadService $leads): RedirectResponse
    {
        abort_unless($lead->source === 'prospect', 404);
        $leads->promote($lead, $request->user());

        return redirect()
            ->route('admin.leads.show', $lead)
            ->with('success', $lead->name.' is now a lead. The prospect emails are stopped.');
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

    public function syncJobNimbus(Lead $lead, JobNimbusService $jobNimbus): RedirectResponse
    {
        $sent = $jobNimbus->pushLead($lead);
        if ($sent === true) {
            return back()->with('success', $lead->name.' is in JobNimbus.');
        }
        if ($sent === null) {
            return back()->with('error', 'JobNimbus is not connected. Add the API key in Hostinger.');
        }

        return back()->with('error', 'JobNimbus did not take this lead.');
    }

    public function note(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:4000']]);
        $lead->log($request->user(), 'note', $data['body']);

        return back()->with('success', 'Note added.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer'],
            'action' => ['required', 'in:status,assign,delete'],
            'status' => ['required_if:action,status', 'nullable', 'in:'.implode(',', Lead::STATUSES)],
            'assigned_to' => ['required_if:action,assign', 'nullable', 'string', 'max:20'],
        ], [
            'ids.required' => 'Select at least one lead.',
            'action.required' => 'Choose what to do with the selected leads.',
            'status.required_if' => 'Choose a status.',
            'assigned_to.required_if' => 'Choose who should own these leads.',
        ]);

        $leads = Lead::query()->exceptProspects()->whereIn('id', $data['ids'])->get();
        if ($leads->isEmpty()) {
            return back()->with('error', 'Those leads are no longer on this list.');
        }

        if ($data['action'] === 'delete') {
            $count = $leads->count();
            $leads->each->delete();

            return back()->with('success', $count.' '.($count === 1 ? 'lead was' : 'leads were').' deleted.');
        }

        if ($data['action'] === 'status') {
            foreach ($leads as $lead) {
                if ($lead->status === $data['status']) {
                    continue;
                }
                $lead->update(['status' => $data['status']]);
                $lead->log($request->user(), 'updated', 'status → '.$data['status']);
            }

            return back()->with('success', $leads->count().' '.($leads->count() === 1 ? 'lead is' : 'leads are').' now '.$data['status'].'.');
        }

        $owner = $data['assigned_to'] === 'none' ? null : (int) $data['assigned_to'];
        if ($owner !== null && ! User::query()->whereKey($owner)->exists()) {
            return back()->with('error', 'Choose who should own these leads.');
        }
        $ownerName = $owner ? User::query()->whereKey($owner)->value('name') : 'Unassigned';
        foreach ($leads as $lead) {
            if ((int) $lead->assigned_to === (int) $owner) {
                continue;
            }
            $lead->update(['assigned_to' => $owner]);
            $lead->log($request->user(), 'updated', 'assigned_to → '.$ownerName);
        }

        return back()->with('success', $leads->count().' '.($leads->count() === 1 ? 'lead assigned' : 'leads assigned').' to '.$ownerName.'.');
    }

    public function destroy(Request $request, Lead $lead): RedirectResponse
    {
        $request->validate([
            'spam' => ['accepted'],
            'confirm_word' => ['required', 'in:DELETE'],
        ]);

        $name = $lead->name;
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('success', $name.' was deleted.');
    }
}
