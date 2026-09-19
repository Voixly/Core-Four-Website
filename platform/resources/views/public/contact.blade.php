@extends('layouts.public')
@section('title', 'Contact Core Four Roofing')
@section('description', 'Get your free, no-obligation roof inspection from Core Four Roofing in Tomball, Texas.')

@section('content')
<section class="hero page-hero"><div class="hero-media"></div>
    <div class="wrap">
        <div class="hero-reviews">@include('partials.elfsight-reviews')</div>
        <h1>Get Your Free, No-Obligation Roof Inspection</h1>
        <p class="lead">Talk to Tomball. {{ $officeAddress }} · {{ $officePhone }}</p>
    </div>
</section>
<section class="section">
    <div class="wrap grid-2">
        <div>
            <h2>Request Your Inspection</h2>
            <p>Mon–Sat 7am–7pm. Emergency 24/7.</p>
            <p><a class="btn" href="tel:+1{{ $officePhoneTel }}"><span class="arrow">→</span> Call Us Today</a></p>
        </div>
        <div class="form-card">
            @include('partials.lead-form', ['source' => 'contact'])
        </div>
    </div>
</section>
<section class="section">
    <div class="wrap">
        @include('partials.before-after', [
            'before' => '/images/ba/ad6b4429-cef8-4e0d-8be5-97760a5f48ca-compressed-scaled.webp',
            'after' => '/images/ba/27d812ee-6a31-44e6-a9c3-76435c4aa8b5-compressed-scaled.webp',
            'beforeAlt' => 'Roof before replacement',
            'afterAlt' => 'Roof after replacement',
        ])
    </div>
</section>
@include('partials.coverage')
@include('partials.reviews')
@include('partials.principles')
@endsection
