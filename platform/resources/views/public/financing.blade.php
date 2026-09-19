@extends('layouts.public')
@section('title', 'Financing - Core Four Roofing')
@section('description', 'Stress-free roof financing in Texas. Flexible options and fast approvals for residential and commercial projects.')

@section('content')
<section class="hero page-hero"><div class="hero-media"></div>
    <div class="wrap">
        <div class="hero-reviews">@include('partials.elfsight-reviews')</div>
        <p class="kicker">Financing</p>
        <h1>Stress-Free Roof Financing in Texas</h1>
        <p class="lead">Smart solutions for your roofing investment. We make the financial side of roofing just as seamless as the installation itself.</p>
        <div class="hero-actions">
            <a class="btn" href="tel:+1{{ $officePhoneTel }}"><span class="arrow">→</span> Call for Financing Now</a>
        </div>
    </div>
</section>
<section class="section">
    <div class="wrap grid-2">
        <div>
            <h2>Why Finance Your Roofing Project With Us?</h2>
            <div class="grid-2">
                <div class="card"><h3>Keep Cash on Hand</h3></div>
                <div class="card"><h3>Fast, Paperless Approvals</h3></div>
                <div class="card"><h3>Flexible Payment Terms</h3></div>
                <div class="card"><h3>Upgrade Your Materials</h3></div>
            </div>
        </div>
        <div class="form-card">
            <h3>Ready to Fund Your New Roof?</h3>
            <p>Speak with a Core Four team member about current financing promotions.</p>
            @include('partials.lead-form', ['source' => 'financing', 'cta' => 'Call to Apply Now'])
        </div>
    </div>
</section>
<section class="section section-alt">
    <div class="wrap">
        @include('partials.before-after', [
            'before' => '/images/ba/core_four_residential_hero_before.webp',
            'after' => '/images/ba/core_four_residential_hero_after.webp',
            'beforeAlt' => 'Home before Core Four roof',
            'afterAlt' => 'Home after Core Four roof',
        ])
    </div>
</section>
@include('partials.reviews')
@endsection
