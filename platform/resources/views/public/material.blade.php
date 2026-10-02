@extends('layouts.public')

@section('title', $page['title'])
@section('description', $page['description'])
@section('canonical', $page['canonical'])
@section('audience', 'residential')
@section('schema')
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@graph' => $page['schema']], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('content')
<section class="hero page-hero">
    <div class="hero-media hero-media--res"></div>
    <div class="wrap">
        <nav class="city-crumbs" aria-label="Breadcrumb">
            <a href="{{ url('/') }}">Home</a>
            <span aria-hidden="true">/</span>
            <a href="{{ url('/residential-roofing/') }}">Residential</a>
            <span aria-hidden="true">/</span>
            <span>{{ $page['h1'] }}</span>
        </nav>
        <p class="kicker">{{ $page['kicker'] }}</p>
        <h1>{{ $page['h1'] }}</h1>
        <p class="lead city-lead">{{ $page['lead'] }}</p>
        <div class="hero-actions">
            <a class="btn" href="#inspect">Get a free roof inspection</a>
            <a class="btn btn-ghost" href="tel:+1{{ $officePhoneTel }}">{{ $officePhone }}</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap grid-2">
        <div class="city-about">
            <img src="{{ $page['image'] }}" alt="{{ $page['image_alt'] }}" width="1200" height="800" style="width:100%;height:auto;margin-bottom:1.25rem">
            @foreach($page['paragraphs'] as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
        <div class="form-card" id="inspect">
            <h3>{{ $page['form_title'] }}</h3>
            <p>{{ $page['form_note'] }}</p>
            @include('partials.lead-form', [
                'source' => 'website',
                'type' => 'residential',
                'cta' => 'Get a free home inspection',
            ])
        </div>
    </div>
</section>

<section class="section section--tight">
    <div class="wrap">
        <div class="city-services">
            @foreach($page['points'] as $point)
                <div class="card">
                    <h3>{{ $point['title'] }}</h3>
                    <p>{{ $point['body'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@include('partials.service-reading', ['reads' => $page['reads'], 'heading' => 'From the crew'])

<section class="section city-faq-section">
    <div class="wrap grid-2">
        <div>
            <h2>Questions before we get on the roof</h2>
            <p>The shop is in Tomball. The work is the Houston suburbs where these roofs actually are.</p>
        </div>
        <div class="faq city-faq">
            @foreach($page['faqs'] as $index => $faq)
                <details @if($index === 0) open @endif>
                    <summary>{{ $faq['q'] }}</summary>
                    <p>{{ $faq['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

@include('partials.guide-cta', ['context' => 'residential'])
@include('partials.reviews')
@endsection
