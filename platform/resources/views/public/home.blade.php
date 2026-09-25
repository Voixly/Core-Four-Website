@extends('layouts.public')

@section('title', \App\Support\SiteSeo::DEFAULT_TITLE)
@section('description', \App\Support\SiteSeo::DEFAULT_DESCRIPTION)

@section('content')
<section class="hero">
    <div class="hero-media"></div>
    <div class="wrap--wide hero-grid">
        <div class="hero-copy">
            <div class="hero-reviews">
                @include('partials.elfsight-reviews')
            </div>
            <h1>Roof repair in Tomball and commercial roofing in Houston</h1>
            <p class="lead">The shop is on Highway 249 in Tomball. Houston buildings and northwest-side homes are the daily route. Austin and Dallas jobs are scheduled from here.</p>
            <div class="hero-actions">
                <a class="btn" href="tel:+1{{ $officePhoneTel }}">Call Us Today <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="hero-stage">
            <div class="float-card float-top">
                <h5>Warranty</h5>
                <p>Lifetime Workmanship</p>
            </div>
            @include('partials.before-after', [
                'before' => '/images/ba/core-four-TPO-roof-before.webp',
                'after' => '/images/ba/core-four-TPO-roof-after.webp',
                'beforeAlt' => 'Core Four TPO roof before',
                'afterAlt' => 'Core Four TPO roof after',
            ])
            <div class="float-card float-bot">
                <h5>Accredited</h5>
                <p>BBB A+ Rating</p>
            </div>
        </div>
    </div>
</section>

<section class="section section--tight">
    <div class="wrap--full">
        <h2 class="logos-heading">Reliable Commercial &amp; Residential Roof Repair, Replacement &amp; Maintenance</h2>
        @include('partials.logo-tracks')
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="solutions-head">
            <h2 class="title-xl">Roofing Solutions that work for your bottom line</h2>
            <a class="btn" href="{{ url('/service-areas/') }}">See cities we serve <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="solutions-grid" style="margin-top:40px">
            <div class="solutions-col">
                <div class="solution-card solution-card--green">
                    <span class="pill pill--white">Commercial</span>
                    <h3>Protecting Your Assets</h3>
                    <p>From TPO to EPDM and Metal, we provide durable, energy-efficient commercial roofing systems designed to last decades. <a href="{{ url('/commercial-roofing/') }}">Commercial roofing</a></p>
                    <img src="/images/commercial-card.webp" alt="Commercial roofing project by Core Four Roofing">
                </div>
                @include('partials.before-after', [
                    'before' => '/images/ba/Decra-12-scaled.jpg',
                    'after' => '/images/ba/Decra-11-scaled.jpg',
                    'beforeAlt' => 'Apartment roof before Decra replacement',
                    'afterAlt' => 'Apartment roof after Decra replacement',
                ])
            </div>
            <div class="solutions-col">
                <div class="solution-card solution-card--white">
                    <span class="pill pill--green">Residential</span>
                    <h3>We Know Texas Roofs</h3>
                    <p>Premium asphalt shingles and architectural metal roofs to boost curb appeal and weather protection. <a href="{{ url('/residential-roofing/') }}">Residential roofing</a></p>
                    <div class="solution-media">
                        <img src="/images/residential-card.webp" alt="Residential roofing project by Core Four Roofing">
                    </div>
                </div>
                <div class="emergency-card">
                    <h4>24/7 Emergency</h4>
                    <p>Storm damage? We offer rapid response roof tarping and full insurance claim advocacy.</p>
                    <a class="btn" href="{{ url('/storm-emergency/') }}">Get Help Now <i class="fas fa-arrow-right"></i></a>
                    <p class="emergency-guide"><a href="/guides/houston-homeowner-storm-checklist/">Or grab the storm checklist</a></p>
                </div>
                <div class="award-card">
                    <h4>Angi Super Service Award, 2024</h4>
                </div>
            </div>
        </div>
    </div>
</section>

@if(isset($resCities) && $resCities->isNotEmpty())
<section class="section">
    <div class="wrap">
        <p class="kicker">Houston metro</p>
        <h2>Roofing in your city</h2>
        <p class="lead">Houston-area cities we work every week, plus scheduled jobs in Austin and Dallas–Fort Worth.</p>
        <div class="city-chips city-chips--links">
            @foreach($resCities as $city)
                <a href="{{ url($city->path()) }}">{{ $city->name }}</a>
            @endforeach
            <a href="{{ url('/residential-roofing-in-tx/') }}">All Texas cities</a>
        </div>
        @if(isset($commCities) && $commCities->isNotEmpty())
            <h3 class="city-dir-sub">Commercial roofing</h3>
            <div class="city-chips city-chips--links">
                @foreach($commCities as $city)
                    <a href="{{ url($city->path()) }}">{{ $city->name }}</a>
                @endforeach
                <a href="{{ url('/commercial-roofing-in-tx/') }}">All commercial cities</a>
            </div>
        @endif
    </div>
</section>
@endif

@include('partials.coverage')
@include('partials.steps')
@include('partials.guide-cta', ['context' => 'home'])
@include('partials.reviews')
@include('partials.principles')
@include('partials.instagram')
@endsection
