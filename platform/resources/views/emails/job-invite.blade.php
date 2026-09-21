@extends('emails.layout')

@section('content')
    <p style="margin:0 0 8px;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#269B48">Job portal</p>
    <h1 style="margin:0 0 16px;font-size:28px;line-height:1.2;color:#0f2418;font-family:Arial,Helvetica,sans-serif">Hi {{ $first }}, track your Core Four job</h1>
    <p style="margin:0 0 16px">Job <strong>{{ $invite->job->number }}</strong>@if($invite->job->city) in {{ $invite->job->city }}@endif is in our pipeline. Set a password and you can see the current stage and upload anything the office asked for.</p>
    <p style="margin:0">This link expires in 7 days. We never post a review for you from this portal.</p>
@endsection
