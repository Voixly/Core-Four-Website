@extends('emails.layout')

@section('content')
    <p style="margin:0 0 8px;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#269B48">{{ $job->number }}</p>
    <h1 style="margin:0 0 16px;font-size:28px;line-height:1.2;color:#0f2418;font-family:Arial,Helvetica,sans-serif">{{ $job->stage?->customer_label ?: $job->stage?->name }}</h1>
    <p style="margin:0 0 16px">Your {{ $job->pipeline?->name }} job moved forward. Open the portal for the next step and any files we still need.</p>
    @if($job->customer_summary)
        <p style="margin:0;padding:16px;background:#f3f0e9;border-radius:12px">{{ $job->customer_summary }}</p>
    @endif
@endsection
