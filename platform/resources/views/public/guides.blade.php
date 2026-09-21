@extends('layouts.public')
@section('title', 'Free Roofing Guides | Core Four Roofing')
@section('description', 'Free Core Four PDFs for Houston homeowners and property managers — storm checklist, insurance walkthrough, replacement timeline, and commercial scorecard.')
@section('content')
<section class="review-hero">
    <div class="wrap review-wrap">
        <p class="kicker">Free PDFs</p>
        <h1>Guides for homeowners and property managers</h1>
        <p class="review-lede">Leave your info, we email the file. That starts the matching 12-month sequence from the Tomball office — unsubscribe anytime.</p>
    </div>
</section>
<section class="section section--tight">
    <div class="wrap">
        <div class="guide-cta-grid">
            @foreach($guides as $guide)
                <a class="guide-cta-card guide-cta-card--light" href="{{ $guide->path() }}">
                    <span class="pill pill--green">{{ $guide->audience }}</span>
                    <i class="fas {{ $guide->icon() }}" aria-hidden="true"></i>
                    <h3>{{ $guide->title }}</h3>
                    <p>{{ $guide->excerpt }}</p>
                    <span class="guide-cta-link">{{ $guide->ctaLabel() }} <i class="fas fa-arrow-right"></i></span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
