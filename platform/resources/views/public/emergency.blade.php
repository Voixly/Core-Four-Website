@extends('layouts.public')
@section('title', '24/7 Emergency Roofing | Core Four Roofing')
@section('description', 'Storm damage? Rapid-response roof tarping and leak repair across Houston, Dallas, and Austin. Call (281) 541-0027.')

@section('content')
<section class="hero page-hero"><div class="hero-media"></div>
    <div class="wrap">
        <div class="hero-reviews">@include('partials.elfsight-reviews')</div>
        <p class="kicker">Storm &amp; Emergency</p>
        <h1>24/7 Emergency Tarping &amp; Repair</h1>
        <p class="lead">Storm damage? We offer rapid response roof tarping and full insurance claim advocacy. If water is in the house or the suite, call now.</p>
        <div class="hero-actions">
            <a class="btn" href="tel:+1{{ $officePhoneTel }}"><span class="arrow">→</span> Get Help Now</a>
        </div>
    </div>
</section>
<section class="section">
    <div class="wrap grid-2">
        <div>
            <h2>Rapid Response Across the Lone Star State</h2>
            <p>With crews across Texas, we deploy emergency teams quickly to the areas hit hardest by severe weather.</p>
            <p>Office {{ $officePhone }} — 24/7.</p>
        </div>
        <div class="form-card">
            @include('partials.lead-form', ['source' => 'emergency', 'cta' => 'Call me back now'])
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
