@extends('layouts.public')

@section('title', 'Residential Roofing - Core Four Roofing')
@section('description', 'Reliable residential roofing in Texas. Storm repairs, replacements, and premium materials across Houston, Dallas, and Austin.')
@section('audience', 'residential')

@section('content')
<section class="hero page-hero">
    <div class="hero-media hero-media--res"></div>
    <div class="wrap">
        <div class="hero-reviews">@include('partials.elfsight-reviews')</div>
        <p class="kicker">Residential</p>
        <h1>Reliable Residential Roofing in Texas</h1>
        <p class="lead">Protect your home, your family, and your biggest investment. From fast storm damage repairs to complete roof replacements, Core Four Roofing delivers top-tier craftsmanship across Houston, Dallas, and Austin.</p>
        <div class="hero-actions">
            <a class="btn" href="tel:+1{{ $officePhoneTel }}"><span class="arrow">→</span> Call Us Today</a>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="wrap">
        <h2>Built on the Core Four Principles</h2>
        <p>We know that dealing with roof leaks, storm damage, or replacements can be stressful. We take the headache out of home roofing by operating strictly on our core principles.</p>
        <div class="grid-4" style="margin-top:1.4rem">
            <div class="card"><h3>Affordability</h3><p>Transparent estimates and financing so you can protect the house without a cheap-roof trap.</p></div>
            <div class="card"><h3>Efficiency</h3><p>Most residential replacements finish in 1–3 days.</p></div>
            <div class="card"><h3>Integrity</h3><p>We never sell what you don’t need. BBB A+.</p></div>
            <div class="card"><h3>Quality</h3><p>Materials built for Texas hail and heat. Lifetime workmanship.</p></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <h2>Complete Roofing Solutions for Your Home</h2>
        <div class="grid-3" style="margin-top:1.4rem">
            <div class="split-card">
                <div class="copy">
                    <h3>Residential Roof Replacement</h3>
                    <p>When your roof reaches the end of its lifespan or suffers severe storm damage, we provide a seamless replacement process. We strip away the old materials, inspect and repair the wood decking, and install a beautiful, durable new roofing system.</p>
                </div>
                <img src="/images/pages/replacement.webp" alt="Residential roof replacement">
            </div>
            <div class="split-card">
                <div class="copy">
                    <h3>Storm Damage &amp; Insurance Claims</h3>
                    <p>We meet adjusters on site, document the damage, and keep the scope honest so you stay the customer.</p>
                    <a href="{{ url('/insurance-claims/') }}">Insurance claims →</a>
                </div>
                <img src="/images/pages/community-1.jpg" alt="Storm damage inspection">
            </div>
            <div class="split-card">
                <div class="copy">
                    <h3>Roof Repair &amp; Maintenance</h3>
                    <p>Missing shingles? Water stains on your ceiling? Don't let a small leak turn into major structural damage. Our expert technicians will track down the source of the problem and provide an affordable, lasting repair.</p>
                </div>
                <img src="/images/pages/repair.webp" alt="Roof repair and maintenance">
            </div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="wrap">
        <h2>Premium Materials &amp; Exterior Upgrades</h2>
        <p>Customize your home's curb appeal with materials engineered to withstand the Texas elements.</p>
        <div class="grid-4" style="margin-top:1.4rem">
            <a class="photo-tile" href="{{ url('/asphalt-shingles/') }}" style="background-image:url('/images/materials/asphalt.webp')"><span>Architectural Asphalt Shingles</span></a>
            <a class="photo-tile" href="{{ url('/metal-roofing/') }}" style="background-image:url('/images/materials/metal.webp')"><span>Standing Seam Metal Roofs</span></a>
            <a class="photo-tile" href="{{ url('/stone-coated-steel/') }}" style="background-image:url('/images/materials/steel.webp')"><span>Stone-Coated Steel</span></a>
            <a class="photo-tile" href="{{ url('/synthetic-roofing/') }}" style="background-image:url('/images/materials/synthetic.webp')"><span>Synthetic Shingles</span></a>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="wrap">
        @include('partials.before-after', [
            'before' => '/images/ba/cfr-decra-tile-before-compressed.webp',
            'after' => '/images/ba/cfr-decra-tile-after-compressed.webp',
            'beforeAlt' => 'Home before stone-coated steel roof',
            'afterAlt' => 'Home after stone-coated steel roof',
        ])
    </div>
</section>

@include('partials.principles')
@include('partials.reviews')
@include('partials.coverage')

<section class="section">
    <div class="wrap">
        <h2>Ready to Upgrade Your Home's Roof?</h2>
        <div class="grid-2">
            <div>
                <p>Get a free, no-obligation inspection from the local roofing experts in Texas.</p>
                <a class="btn" href="tel:+1{{ $officePhoneTel }}"><span class="arrow">→</span> Call Us Today</a>
            </div>
            <div class="form-card" id="inspect">
                <h3>Send us a Message</h3>
                @include('partials.lead-form', ['source' => 'residential', 'type' => 'residential'])
            </div>
        </div>
        <div class="city-list" style="margin-top:2rem">
            @foreach($cities as $city)
                <a href="{{ $city->path() }}">{{ $city->name }}</a>
            @endforeach
        </div>
    </div>
</section>
@endsection
