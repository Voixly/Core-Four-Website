@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="cards">
    <div class="stat"><span>New leads</span><b>{{ $newLeads }}</b></div>
    <div class="stat"><span>Open chats</span><b>{{ $openChats }}</b></div>
    <div class="stat"><span>Inspections today</span><b>{{ $inspections }}</b></div>
    <div class="stat"><span>Mail today / queued / failed</span><b>{{ $mailHealth['sent'] }} / {{ $mailHealth['scheduled'] }} / {{ $mailHealth['failed'] }}</b></div>
    <div class="stat"><span>Review Shield holds</span><b><a href="{{ route('admin.reviews.index', ['status' => 'held']) }}">{{ $heldReviews }}</a></b></div>
    <div class="stat"><span>Open jobs</span><b><a href="{{ route('admin.jobs.index') }}">{{ $openJobs }}</a></b></div>
    <div class="stat"><span>Waiting on docs</span><b>{{ $waitingDocs }}</b></div>
    <div class="stat"><span>Emergency jobs</span><b>{{ $emergencyJobs }}</b></div>
</div>
<div class="grid job-split">
    <div class="panel">
        <h3>Upcoming visits</h3>
        @forelse($upcomingAppointments as $appointment)
            <p>
                <a href="{{ route('admin.jobs.show', ['job' => $appointment->job, 'tab' => 'schedule']) }}"><strong>{{ $appointment->job->number }}</strong></a>
                · {{ $appointment->label() }}
                <br class="page-meta">{{ $appointment->starts_at->timezone(config('app.timezone'))->format('D M j, g:ia') }}
                @if($appointment->assignee) · {{ $appointment->assignee->name }}@endif
            </p>
        @empty
            <p>Nothing on the calendar. <a href="{{ route('admin.schedule') }}">Open the week</a>.</p>
        @endforelse
    </div>
    <div class="panel">
        <h3>Open tasks</h3>
        @forelse($openTasks as $task)
            <p>
                <a href="{{ route('admin.jobs.show', ['job' => $task->job, 'tab' => 'tasks']) }}">{{ $task->title }}</a>
                <span class="page-meta"> · {{ $task->job->number }}@if($task->due_on) · due {{ $task->due_on->format('M j') }}@endif</span>
            </p>
        @empty
            <p>No open job tasks.</p>
        @endforelse
    </div>
</div>
<div class="panel">
    <h3>Unpaid invoices</h3>
    @forelse($unpaidInvoices as $invoice)
        <p>
            <a href="{{ route('admin.jobs.show', ['job' => $invoice->job, 'tab' => 'invoices']) }}">{{ $invoice->number }}</a>
            · ${{ number_format((float) $invoice->amount, 2) }}
            · {{ $invoice->status }}
            @if($invoice->due_on) · due {{ $invoice->due_on->format('M j') }}@endif
        </p>
    @empty
        <p>No open invoices.</p>
    @endforelse
</div>
<div class="panel">
    <h3>Recent leads</h3>
    <table>
        <tr><th>Name</th><th>Type</th><th>Source</th><th>Status</th><th></th></tr>
        @foreach($recentLeads as $lead)
            <tr>
                <td>{{ $lead->name }}</td>
                <td>{{ $lead->type }}</td>
                <td>{{ $lead->source }}</td>
                <td><span class="tag tag-{{ $lead->status }}">{{ $lead->status }}</span></td>
                <td><a href="{{ route('admin.leads.show', $lead) }}">Open</a></td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
