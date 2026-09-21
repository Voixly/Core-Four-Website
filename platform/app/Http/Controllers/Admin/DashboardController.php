<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\EmailSend;
use App\Models\Job;
use App\Models\JobAppointment;
use App\Models\JobDocumentRequest;
use App\Models\JobInvoice;
use App\Models\JobTask;
use App\Models\Lead;
use App\Models\Review;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'newLeads' => Lead::query()->where('status', 'new')->count(),
            'openChats' => Conversation::query()->where('status', 'open')->count(),
            'inspections' => Lead::query()->where('status', 'inspected')->whereDate('updated_at', today())->count(),
            'mailHealth' => [
                'scheduled' => EmailSend::query()->where('status', 'scheduled')->count(),
                'sent' => EmailSend::query()->where('status', 'sent')->whereDate('sent_at', today())->count(),
                'failed' => EmailSend::query()->where('status', 'failed')->count(),
            ],
            'recentLeads' => Lead::query()->latest()->limit(8)->get(),
            'heldReviews' => Review::query()->where('status', 'held')->count(),
            'openJobs' => Job::query()->where('status', 'open')->count(),
            'waitingDocs' => JobDocumentRequest::query()
                ->whereNull('fulfilled_by')
                ->where('required', true)
                ->whereHas('job', fn ($q) => $q->where('status', 'open'))
                ->count(),
            'emergencyJobs' => Job::query()->where('status', 'open')->where('urgency', 'emergency')->count(),
            'upcomingAppointments' => JobAppointment::query()
                ->with(['job.lead', 'assignee'])
                ->where('status', 'scheduled')
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(6)
                ->get(),
            'openTasks' => JobTask::query()
                ->with('job')
                ->where('is_done', false)
                ->whereHas('job', fn ($q) => $q->where('status', 'open'))
                ->orderByRaw('due_on is null')
                ->orderBy('due_on')
                ->limit(6)
                ->get(),
            'unpaidInvoices' => JobInvoice::query()
                ->with('job')
                ->whereIn('status', ['draft', 'sent'])
                ->orderByRaw('due_on is null')
                ->orderBy('due_on')
                ->limit(6)
                ->get(),
        ]);
    }
}
