@extends('layouts.public')
@section('title', 'Page not found | Core Four Roofing')
@section('description', 'That page is gone. Try residential, commercial, or call Core Four Roofing at (281) 541-0027.')
@section('robots', 'noindex, nofollow')
@section('content')
<section class="review-hero">
    <div class="wrap review-wrap">
        <h1>That page is gone</h1>
        <p class="review-lede">If you followed an old link, try the pages below or call {{ $officePhone }}.</p>
        <p>
            <a class="btn" href="{{ url('/residential-roofing/') }}">Residential</a>
            <a class="btn btn--white" href="{{ url('/commercial-roofing/') }}">Commercial</a>
            <a class="btn btn--white" href="{{ url('/contact-core-four-roofing/') }}">Contact</a>
        </p>
    </div>
</section>
@endsection
