@extends('layouts.public')

@section('title', 'Commercial Roofing - Core Four Roofing')
@section('description', 'Texas’ trusted commercial roofing experts. Replacement, maintenance, inspections, and coatings for property managers and business owners.')
@section('audience', 'commercial')

@section('content')
<section class="hero page-hero">
    <div class="hero-media hero-media--comm"></div>
    <div class="wrap">
        <div class="hero-reviews">@include('partials.elfsight-reviews')</div>
        <p class="kicker">Commercial</p>
        <h1>Texas’ Trusted Commercial Roofing Experts</h1>
        <p class="lead">At Core Four Roofing, we understand that a commercial roof is a major capital investment. Property managers, HOA boards, and business owners across Texas trust us because we prioritize safety, efficiency, and transparent communication.</p>
        <div class="hero-actions">
            <a class="btn" href="#survey"><span class="arrow">→</span> Request a Commercial Bid</a>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="wrap">
        <h2>Engineered for Business Continuity</h2>
        <p>We work directly with your team to ensure your facility remains operational while we secure your asset overhead.</p>
        <div class="grid-4" style="margin-top:1.4rem">
            <div class="card"><h3>Safety First</h3><p>Jobsite protocols that keep tenants, staff, and crews protected.</p></div>
            <div class="card"><h3>Zero Disruption</h3><p>Night and weekend windows so the building keeps earning.</p></div>
            <div class="card"><h3>Financial Flexibility</h3><p>Transparent capital planning, not surprise change orders.</p></div>
            <div class="card"><h3>Unmatched Quality</h3><p>Systems engineered for Houston heat, humidity, and hail.</p></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <h2>Comprehensive Commercial Roofing Services</h2>
        <div class="grid-2" style="margin-top:1.4rem">
            <div class="card"><h3>Commercial Roof Replacement &amp; Installation</h3><p>Full tear-off and install when the asset is at the end of its life.</p></div>
            <div class="card"><h3>Repair &amp; Preventative Maintenance</h3><p>Keep leaks off the tenant list and extend the life of the system.</p></div>
            <div class="card"><h3>Commercial Inspections &amp; Condition Reports</h3><p>Documented surveys for boards, lenders, and capital planning.</p></div>
            <div class="card"><h3>Coatings &amp; Restoration</h3><p>Restore a sound roof instead of replacing it early.</p></div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="wrap">
        <h2>Premium Commercial Materials Built for Texas Weather</h2>
        <div class="grid-4" style="margin-top:1.4rem">
            <a class="card" href="{{ url('/tpo-roofing/') }}"><img src="/images/materials/tpo.png" alt="TPO"><h3>TPO</h3><p>Thermoplastic Polyolefin</p></a>
            <a class="card" href="{{ url('/epdm-roofing/') }}"><img src="/images/materials/epdm.png" alt="EPDM"><h3>EPDM</h3><p>Rubber roofing</p></a>
            <a class="card" href="{{ url('/modified-bitumen/') }}"><img src="/images/materials/modbit.png" alt="Modified bitumen"><h3>Modified Bitumen</h3><p>Layered commercial systems</p></a>
            <a class="card" href="{{ url('/tpo-roofing/') }}"><img src="/images/materials/comm-metal.webp" alt="Commercial metal"><h3>Commercial Metal</h3><p>Long-life metal systems</p></a>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        @include('partials.before-after', [
            'before' => '/images/ba/ad6b4429-cef8-4e0d-8be5-97760a5f48ca-compressed-scaled.webp',
            'after' => '/images/ba/27d812ee-6a31-44e6-a9c3-76435c4aa8b5-compressed-scaled.webp',
            'beforeAlt' => 'Commercial roof before replacement',
            'afterAlt' => 'Commercial roof after replacement',
        ])
    </div>
</section>

@include('partials.coverage')
@include('partials.reviews')

<section class="section section-alt">
    <div class="wrap grid-2">
        <div>
            <h2>Proudly Serving Texas Commercial Properties</h2>
            <p>Commercial roofs in Texas face a unique set of challenges, from the blistering summer heat and high humidity of Houston to the severe hail storms in Dallas and Austin. You need a local contractor who understands how to engineer a roof for this exact climate.</p>
        </div>
        <div class="form-card" id="survey">
            <h3>Request a commercial roof survey</h3>
            @include('partials.lead-form', ['source' => 'commercial', 'type' => 'commercial', 'cta' => 'Request a commercial roof survey'])
        </div>
    </div>
    <div class="wrap" style="margin-top:2rem">
        <div class="city-list">
            @foreach($cities as $city)
                <a href="{{ $city->path() }}">{{ $city->name }}</a>
            @endforeach
        </div>
    </div>
</section>
@endsection
