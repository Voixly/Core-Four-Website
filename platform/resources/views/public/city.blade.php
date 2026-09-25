@extends('layouts.public')

@section('title', $seo['title'])
@section('description', $seo['description'])
@section('canonical', $seo['canonical'])
@section('audience', $city->type)
@section('schema')
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@graph' => $seo['schema']], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('content')
<section class="hero page-hero">
    <div class="hero-media {{ $city->type === 'commercial' ? 'hero-media--comm' : 'hero-media--res' }}"></div>
    <div class="wrap">
        <nav class="city-crumbs" aria-label="Breadcrumb">
            <a href="{{ url('/') }}">Home</a>
            <span aria-hidden="true">/</span>
            <a href="{{ url('/service-areas/') }}">Service areas</a>
            <span aria-hidden="true">/</span>
            <span>{{ $city->name }}</span>
        </nav>
        <div class="hero-reviews">@include('partials.elfsight-reviews')</div>
        <p class="kicker">{{ $seo['kicker'] }}</p>
        <h1>{{ $seo['h1'] }}</h1>
        <p class="lead city-lead">{{ $seo['lead'] }}</p>
        <div class="hero-actions">
            <a class="btn" href="#inspect">{{ $city->type === 'commercial' ? 'Request a commercial survey' : 'Get a free roof inspection' }}</a>
            <a class="btn btn-ghost" href="tel:+1{{ $officePhoneTel }}">{{ $officePhone }}</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap grid-2">
        <div class="city-about">
            <h2>{{ $seo['is_hub'] ? 'How Texas coverage works' : 'Roofing in '.$city->name.', '.$city->state }}</h2>
            @foreach($seo['about'] as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
            @if($seo['neighborhoods'] || $seo['zips'])
                <div class="city-facts">
                    @if($seo['neighborhoods'])
                        <div>
                            <h3>{{ $seo['is_hub'] ? 'Markets' : 'Neighborhoods' }}</h3>
                            <div class="city-chips">
                                @foreach($seo['neighborhoods'] as $place)
                                    <span>{{ $place }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($seo['zips'])
                        <div>
                            <h3>ZIP codes</h3>
                            <div class="city-chips">
                                @foreach($seo['zips'] as $zip)
                                    <span>{{ $zip }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($seo['drive'])
                        <p class="city-drive"><strong>From Tomball HQ:</strong> {{ $seo['drive'] }}</p>
                    @endif
                </div>
            @endif
        </div>
        <div class="form-card" id="inspect">
            <h3>{{ $seo['is_hub'] ? 'Request an inspection' : $city->name.' inspection' }}</h3>
            <p>Tell us the address. We call the same day.</p>
            @include('partials.lead-form', [
                'source' => 'city',
                'type' => $city->type,
                'cityName' => $city->name,
                'cta' => $city->type === 'commercial' ? 'Request a commercial roof survey' : 'Get a free home inspection',
            ])
        </div>
    </div>
</section>

<section class="section section--tight">
    <div class="wrap">
        <h2>{{ $city->name }} weather and what fails first</h2>
        <p>{{ $seo['weather'] }}</p>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <h2>{{ $seo['services_heading'] }}</h2>
        <div class="city-services">
            @foreach($seo['services'] as $service)
                <a class="card" href="{{ url($service['href']) }}">
                    <h3>{{ $service['title'] }}</h3>
                    <p>{{ $service['body'] }}</p>
                </a>
            @endforeach
        </div>
        @if($city->type === 'commercial' && in_array($city->slug, ['humble', 'tomball'], true))
            <h3>For property managers</h3>
            <p><a href="{{ url('/commercial-roofing/repair-preventative-maintenance/') }}">Commercial roof repair</a> covers the leak call. <a href="{{ url('/navigating-code-for-commercial-roof-drainage-systems/') }}">Drainage code</a> and <a href="{{ url('/stop-tearing-it-down-how-restoration-extends-your-asset-value-by-ten-plus-years/') }}">coatings versus tear-off</a> are the two write-ups we send with a Humble or Tomball survey.</p>
        @endif
        @if($seo['sibling'])
            <p class="city-switch">
                @if($city->type === 'residential')
                    Looking for a commercial building in {{ $city->name }}?
                    <a href="{{ url($seo['sibling']->path()) }}">Commercial roofing in {{ $city->name }}</a>
                @else
                    Looking for a house in {{ $city->name }}?
                    <a href="{{ url($seo['sibling']->path()) }}">Residential roofing in {{ $city->name }}</a>
                @endif
            </p>
        @endif
    </div>
</section>

<section class="section city-faq-section">
    <div class="wrap grid-2">
        <div>
            <h2>Questions about {{ $city->name }} roofing</h2>
            <p>Straight answers for homeowners and property managers in {{ $seo['county'] }}.</p>
        </div>
        <div class="faq city-faq">
            @foreach($seo['faqs'] as $index => $faq)
                <details @if($index === 0) open @endif>
                    <summary>{{ $faq['q'] }}</summary>
                    <p>{{ $faq['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

@if($seo['nearby']->isNotEmpty())
<section class="section">
    <div class="wrap">
        <h2>{{ $seo['is_hub'] ? 'Cities we serve' : 'Nearby cities we serve' }}</h2>
        <p>Other cities on the same route from Tomball.</p>
        <div class="city-chips city-chips--links">
            @foreach($seo['nearby'] as $near)
                <a href="{{ url($near->path()) }}">{{ $near->name }}</a>
            @endforeach
            @if(! $seo['is_hub'])
                <a href="{{ url($city->type === 'commercial' ? '/commercial-roofing-in-tx/' : '/residential-roofing-in-tx/') }}">All Texas cities</a>
            @endif
        </div>
    </div>
</section>
@endif

@include('partials.guide-cta', ['context' => $city->type === 'commercial' ? 'city-commercial' : 'city-residential'])
@include('partials.reviews')
@include('partials.coverage', [
    'coverage' => [
        'title' => $seo['is_hub'] ? 'Texas coverage from Tomball' : 'Serving '.$city->name.' from Tomball',
        'lede' => $seo['drive'] ?: 'Core Four Roofing service coverage',
    ],
])
@endsection
