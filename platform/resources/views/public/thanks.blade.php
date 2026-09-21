@extends('layouts.public')
@section('title', 'We got your request | Core Four Roofing')
@section('description', 'A Core Four estimator will call you shortly. If water is coming in now, call (281) 541-0027 for a same-day tarp.')
@section('robots', 'noindex, nofollow')
@section('content')
<section class="review-hero">
    <div class="wrap review-wrap">
        <p class="kicker">Thank you</p>
        <h1>A Core Four estimator will call you shortly.</h1>
        <p class="review-lede">If water is coming in now, do not wait. Call {{ $officePhone }} for a same-day tarp.</p>
        <p>
            <a class="btn" href="tel:+1{{ $officePhoneTel }}">Call {{ $officePhone }}</a>
            <a class="btn btn--white" href="{{ url('/contact-core-four-roofing/') }}">Contact the office</a>
        </p>
    </div>
</section>
@endsection
