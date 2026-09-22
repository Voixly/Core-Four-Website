@extends('layouts.public')
@section('title', 'Application received | Core Four Roofing')
@section('description', 'Core Four Roofing received your job application. The Tomball office will call if we want to meet.')
@section('robots', 'noindex, nofollow')
@section('content')
<section class="review-hero">
    <div class="wrap review-wrap">
        <p class="kicker">Careers</p>
        <h1>We got your application.</h1>
        <p class="review-lede">The office will call if we want to set up a conversation. You can also reach the shop at {{ $officePhone }}.</p>
        <p>
            <a class="btn" href="tel:+1{{ $officePhoneTel }}">Call {{ $officePhone }}</a>
            <a class="btn btn--white" href="{{ url('/careers/') }}">Back to careers</a>
        </p>
    </div>
</section>
@endsection
