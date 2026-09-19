@extends('layouts.public')
@section('title', 'We got your request | Core Four Roofing')
@section('content')
<section class="section">
    <div class="wrap" style="max-width:40rem">
        <p class="kicker">Thank you</p>
        <h1>A Core Four estimator will call you shortly.</h1>
        <p>If water is coming in now, do not wait — call {{ $officePhone }} for a same-day tarp.</p>
        <p>
            <a class="btn" href="tel:+1{{ $officePhoneTel }}">Call {{ $officePhone }}</a>
            <a class="btn" href="{{ url('/guides/') }}">Grab a storm guide</a>
        </p>
    </div>
</section>
@endsection
