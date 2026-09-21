@extends('layouts.account')
@section('title', 'Your jobs')
@section('content')
<p class="kicker">Customer portal</p>
<h1>Your Core Four jobs</h1>
@forelse($jobs as $job)
    <a class="account-job-card" href="{{ route('account.jobs.show', $job) }}">
        <span class="pill pill--green">{{ $job->number }}</span>
        <h3>{{ $job->pipeline?->name }}</h3>
        <p>{{ $job->customerVisibleStage()?->customer_label ?: 'In progress' }}@if($job->city) · {{ $job->city }}@endif</p>
    </a>
@empty
    <p>No jobs are linked to this login yet. Ask the Tomball office to send an invite.</p>
@endforelse
@endsection
