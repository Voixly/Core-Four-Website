@extends('layouts.public')
@section('title', 'About Core Four Roofing')
@section('description', 'Built on integrity. Driven by excellence. Houston-suburb roofing with lifetime workmanship and a BBB A+ rating.')

@section('content')
<section class="hero page-hero"><div class="hero-media"></div>
    <div class="wrap">
        <div class="hero-reviews">@include('partials.elfsight-reviews')</div>
        <p class="kicker">Roofing Done The Right Way</p>
        <h1>Built on Integrity. Driven by Excellence.</h1>
        <p class="lead">We are a Texas roofing company that still answers the phone after the check clears. 1000+ jobs. BBB A+. Lifetime workmanship.</p>
        <div class="hero-actions">
            <a class="btn" href="{{ url('/contact-core-four-roofing/') }}"><span class="arrow">→</span> Schedule a Residential Inspection</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap grid-2">
        <div>
            <h2>Standing Strong With Texas</h2>
            <h3>When disaster strikes, we step up.</h3>
            <p>Recently, when devastating tornadoes tore through Texas, our team immediately deployed to provide emergency tarping services to affected homeowners. We believe that true community partnership means being there when you need us most.</p>
        </div>
        <img src="/images/pages/community-1.jpg" alt="Core Four serving Texas communities" style="border-radius:24px;height:320px;object-fit:cover;width:100%">
    </div>
</section>

@include('partials.principles')
@include('partials.coverage')

<section class="section section-alt">
    <div class="wrap">
        <h2>Unmatched Protection &amp; Stress-Free Solutions</h2>
        <div class="grid-2">
            <div class="card"><h3>Flexible Financing</h3><p>Stress-free options so you can protect the property without waiting on cash.</p><a href="{{ url('/financing/') }}">Financing →</a></div>
            <div class="card"><h3>Extended Warranties</h3><p>Lifetime workmanship on the work we do, plus manufacturer coverage on the system.</p></div>
        </div>
        <p style="margin-top:2rem"><a class="btn" href="{{ url('/contact-core-four-roofing/') }}"><span class="arrow">→</span> Ready to Experience the Core Four Difference?</a></p>
    </div>
</section>
@include('partials.reviews')
@endsection
