@extends('layouts.public')
@section('title', 'Insurance - Core Four Roofing')
@section('description', 'Expert roof insurance claim assistance in Texas. We meet adjusters on site and document storm damage.')

@section('content')
<section class="hero page-hero"><div class="hero-media"></div>
    <div class="wrap">
        <div class="hero-reviews">@include('partials.elfsight-reviews')</div>
        <p class="kicker">Insurance</p>
        <h1>Expert Roof Insurance Claim Assistance in Texas</h1>
        <p class="lead">A local advocate on your side. We speak the language of insurance companies and keep you the customer.</p>
        <div class="hero-actions">
            <a class="btn" href="#claim"><span class="arrow">→</span> Schedule a Storm Damage Inspection</a>
        </div>
    </div>
</section>
<section class="section">
    <div class="wrap grid-2">
        <div>
            <h2>Navigating the Roof Insurance Claim Process</h2>
            <h3>How We Maximize Your Storm Recovery</h3>
            <p>Our team speaks the language of insurance companies. We use the same estimating software as the major carriers to ensure our bids are accurate, transparent, and rapidly approved.</p>
            <div class="grid-3" style="margin-top:1.2rem">
                <div class="card"><h3>Detailed Damage Documentation</h3></div>
                <div class="card"><h3>On-Site Adjuster Meetings</h3></div>
                <div class="card"><h3>Strict Code Compliance</h3></div>
            </div>
        </div>
        <div class="form-card" id="claim">
            <h3>Suspect Storm Damage? Act Fast.</h3>
            <p>Request an urgent, no-obligation storm damage inspection. Our experts will document the damage and help you start the recovery process today.</p>
            @include('partials.lead-form', ['source' => 'insurance', 'cta' => 'Schedule Your Inspection'])
        </div>
    </div>
</section>
<section class="section section-alt">
    <div class="wrap">
        @include('partials.before-after', [
            'before' => '/images/ba/core_four_emergency_repair_before-compressed.webp',
            'after' => '/images/ba/core_four_emergency_repair_after-compressed.webp',
            'beforeAlt' => 'Storm-damaged roof before repair',
            'afterAlt' => 'Roof after emergency repair',
        ])
    </div>
</section>
@include('partials.coverage')
@include('partials.reviews')
@endsection
