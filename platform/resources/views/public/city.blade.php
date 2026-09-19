@extends('layouts.public')

@section('title', ($city->type === 'commercial' ? 'Commercial' : 'Residential').' Roofing in '.$city->name.', TX | Core Four')
@section('audience', $city->type)
@section('description', 'Core Four Roofing serves '.$city->name.' with inspections, storm work, and lifetime workmanship. Call (281) 541-0027.')

@section('content')
<section class="hero page-hero"><div class="hero-media {{ $city->type === 'commercial' ? 'hero-media--comm' : 'hero-media--res' }}"></div>
    <div class="wrap">
        <div class="hero-reviews">@include('partials.elfsight-reviews')</div>
        <p class="kicker">{{ $city->metro }} · {{ ucfirst($city->type) }}</p>
        <h1>{{ $city->type === 'commercial' ? 'Commercial roofing' : 'Home roofing' }} in {{ $city->name }}</h1>
        <p class="lead">
            @if($city->type === 'commercial')
                Property in {{ $city->name }} cannot sit with a slow leak. We survey, bid, and schedule around tenants.
            @else
                Hail and heat hit {{ $city->name }} roofs hard. We inspect, document the claim, and replace in 1–3 days.
            @endif
        </p>
        <div class="hero-actions">
            <a class="btn" href="#inspect">{{ $city->type === 'commercial' ? 'Request a survey' : 'Get a free home inspection' }}</a>
            <a class="btn btn-ghost" href="tel:+1{{ $officePhoneTel }}">{{ $officePhone }}</a>
        </div>
    </div>
</section>
<section class="section">
    <div class="wrap grid-2">
        <div>
            <h2>Why {{ $city->name }} calls Tomball</h2>
            <p>HQ is 22955 TX-249 Ste 26. Crews run the Houston suburbs daily. Austin and Dallas pages stay for search; paid ads stay Houston-suburb focused.</p>
            <div class="grid-3">
                <div class="card"><h3>Inspect</h3><p>Local photos, not a generic report.</p></div>
                <div class="card"><h3>Install</h3><p>1–3 days on most homes. Nights/weekends on commercial.</p></div>
                <div class="card"><h3>Warranty</h3><p>Lifetime workmanship. Same phone after the job.</p></div>
            </div>
        </div>
        <div class="form-card" id="inspect">
            <h3>{{ $city->name }} request</h3>
            @include('partials.lead-form', [
                'source' => 'city',
                'type' => $city->type,
                'cityName' => $city->name,
                'cta' => $city->type === 'commercial' ? 'Request a commercial roof survey' : 'Get a free home inspection',
            ])
        </div>
    </div>
</section>
@include('partials.reviews')
@include('partials.coverage')
@endsection
