@extends('layouts.public')
@section('title', 'We got your request | Core Four Roofing')
@section('description', 'A Core Four estimator will call you shortly. If water is coming in now, call (281) 541-0027 for a same-day tarp.')
@section('robots', 'noindex, nofollow')
@section('content')
<section class="section">
    <div class="wrap" style="max-width:40rem">
        <p class="kicker">Thank you</p>
        <h1>A Core Four estimator will call you shortly.</h1>
        <p>If water is coming in now, do not wait — call {{ $officePhone }} for a same-day tarp.</p>
        <p>
            <a class="btn" href="tel:+1{{ $officePhoneTel }}">Call {{ $officePhone }}</a>
            <a class="btn" href="/guides/houston-homeowner-storm-checklist/">Grab a storm guide</a>
            <a class="btn" href="/reviews/">Rate a finished job</a>
        </p>
    </div>
</section>
@endsection
