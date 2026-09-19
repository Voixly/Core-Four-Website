@extends('layouts.public')
@section('title', 'Service Areas | Core Four Roofing')
@section('description', 'Core Four Roofing provides premium commercial and residential roofing across Texas. Service hubs in Houston, Dallas, and Austin.')

@section('content')
<section class="hero page-hero"><div class="hero-media"></div>
    <div class="wrap">
        <div class="hero-reviews">@include('partials.elfsight-reviews')</div>
        <h1>Protect Your Business, Secure Your Home</h1>
        <p class="lead">Core Four Roofing provides premium commercial and residential roofing across Texas. Providing integrity, efficiency, quality, and affordability.</p>
    </div>
</section>
<section class="section">
    <div class="wrap">
        <h2>Residential</h2>
        <div class="city-list">
            @foreach($residential as $city)<a href="{{ $city->path() }}">{{ $city->name }}</a>@endforeach
        </div>
        <h2 style="margin-top:2.4rem">Commercial</h2>
        <div class="city-list">
            @foreach($commercial as $city)<a href="{{ $city->path() }}">{{ $city->name }}</a>@endforeach
        </div>
    </div>
</section>
<section class="section section-alt">
    <div class="wrap solutions-ba">
        @include('partials.before-after', [
            'before' => '/images/ba/Decra-12-scaled.jpg',
            'after' => '/images/ba/Decra-11-scaled.jpg',
            'beforeAlt' => 'Apartment roof before Decra replacement',
            'afterAlt' => 'Apartment roof after Decra replacement',
        ])
    </div>
</section>
@include('partials.coverage')
@include('partials.reviews')
@endsection
