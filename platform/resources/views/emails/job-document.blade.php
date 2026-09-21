@extends('emails.layout')

@section('content')
    <p style="margin:0 0 8px;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#269B48">Customer upload</p>
    <h1 style="margin:0 0 16px;font-size:28px;line-height:1.2;color:#0f2418;font-family:Arial,Helvetica,sans-serif">{{ $document->original_name }}</h1>
    <p style="margin:0">{{ $job->lead?->name ?: 'A customer' }} uploaded a file on job <strong>{{ $job->number }}</strong>@if($job->city) in {{ $job->city }}@endif.</p>
@endsection
