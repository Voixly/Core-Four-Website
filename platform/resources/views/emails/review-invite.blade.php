@extends('emails.layout')

@section('content')
    <p style="margin:0 0 8px;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#269B48">Private rating</p>
    <h1 style="margin:0 0 16px;font-size:28px;line-height:1.2;color:#0f2418;font-family:Arial,Helvetica,sans-serif">Hi {{ $first }}, how did we do?</h1>
    <p style="margin:0 0 16px">This stays between you and Core Four first. It is not a Google or Yelp review — just a 10-second check so we know if the job landed right.</p>
    @if($review->job)
        <p style="margin:0 0 16px">Job: <strong>{{ $review->job }}</strong>@if($review->city) in {{ $review->city }}@endif.</p>
    @endif
    <p style="margin:0">Tap below, pick 1–5 stars, and we’ll take it from there.</p>
@endsection
