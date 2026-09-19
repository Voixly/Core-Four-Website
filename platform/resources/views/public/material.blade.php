@extends('layouts.public')

@section('title', $material['title'].' - Core Four Roofing')
@section('description', \Illuminate\Support\Str::limit(strip_tags($material['lead'] ?? ''), 155))
@section('audience', $material['type'] ?? 'residential')

@section('content')
<section class="hero">
    <div class="hero-media" @if(!empty($material['hero_image'])) style="background-image:url('{{ $material['hero_image'] }}')" @endif></div>
    <div class="wrap--wide hero-grid">
        <div class="hero-copy">
            <div class="hero-reviews">
                @include('partials.elfsight-reviews')
            </div>
            <h1>{{ $material['title'] }}</h1>
            <p class="lead">{{ $material['lead'] }}</p>
            <div class="hero-actions">
                <a class="btn" href="tel:+1{{ $officePhoneTel }}">{{ $material['hero_btn'] ?? 'Call for a Free Roof Estimate' }} <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        @if(!empty($material['certs']))
            <div class="hero-stage">
                <div class="hero-creds">
                    @foreach($material['certs'] as $cert)
                        <img src="{{ $cert }}" alt="Core Four Roofing certification">
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

<section class="section">
    <div class="wrap copy-split">
        <div>
            <div class="copy-card">
                <h2>{{ $material['what_heading'] }}</h2>
                @foreach($material['what_body'] as $para)
                    <p>{{ $para }}</p>
                @endforeach
            </div>
            @if(!empty($material['what_btn']))
                <a class="btn" style="margin-top:24px" href="{{ url('/contact-core-four-roofing/') }}">{{ $material['what_btn'] }} <i class="fas fa-arrow-right"></i></a>
            @endif
        </div>
        <div class="media-stack">
            @foreach($material['what_images'] as $img)
                <img src="{{ $img }}" alt="{{ $material['title'] }}">
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap benefits-grid">
        <div class="benefits-intro">
            <span class="pill pill--green">{{ $material['benefits_pill'] }}</span>
            <h2>{{ $material['benefits_heading'] }}</h2>
            <p>{{ $material['benefits_lead'] }}</p>
            <a class="btn" href="tel:+1{{ $officePhoneTel }}">{{ $material['benefits_btn'] }} <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="benefit-cards">
            @foreach($material['benefits'] as $benefit)
                <div class="benefit-card">
                    <h3>{{ $benefit['title'] }}</h3>
                    <p>{{ $benefit['body'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section section--steps faq-section">
    <div class="wrap">
        <h2 class="title-xl">{{ $material['faq_heading'] }}</h2>
        <div class="faq-grid">
            <div>
                <div class="faq">
                    @foreach($material['faqs'] as $i => $faq)
                        <details>
                            <summary><span class="step-num">{{ $i + 1 }}</span> {{ $faq['title'] }}</summary>
                            <p>{{ $faq['body'] }}</p>
                        </details>
                    @endforeach
                </div>
                <a class="btn btn--grey" href="{{ url('/contact-core-four-roofing/') }}">{{ $material['faq_btn'] ?? 'Ask us a question' }} <i class="fas fa-arrow-right"></i></a>
            </div>
            @if(!empty($material['faq_before']) && !empty($material['faq_after']))
                @include('partials.before-after', [
                    'before' => $material['faq_before'],
                    'after' => $material['faq_after'],
                    'beforeAlt' => $material['title'].' before',
                    'afterAlt' => $material['title'].' after',
                ])
            @elseif(!empty($material['faq_image']))
                <img class="rounded-media" src="{{ $material['faq_image'] }}" alt="{{ $material['title'] }}">
            @endif
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <h2 class="title-xl" style="max-width:800px">{{ $material['value_heading'] }}</h2>
        @if(!empty($material['value_lead']))
            <p style="max-width:800px;margin-bottom:36px">{{ $material['value_lead'] }}</p>
        @endif
        <div class="value-grid">
            @foreach($material['values'] as $i => $value)
                <div class="value-card">
                    @if($i === 1 && !empty($material['value_images'][1]))
                        <img class="value-media" src="{{ $material['value_images'][1] }}" alt="{{ $value['title'] }}">
                    @endif
                    <h3>{{ $value['title'] }}</h3>
                    <p>{{ $value['body'] }}</p>
                    @if($i === 0 && !empty($material['value_images'][0]))
                        <img class="value-media" src="{{ $material['value_images'][0] }}" alt="{{ $value['title'] }}">
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <h2 class="title-xl">{{ $material['cta_heading'] }}</h2>
        <div class="copy-split" style="margin-top:32px">
            <div class="form-card form-card--green">
                <h3>{{ $material['form_heading'] }}</h3>
                <p>{{ $material['form_lead'] }}</p>
                @include('partials.lead-form', ['source' => $slug, 'type' => $material['type'] ?? 'residential', 'cta' => 'Submit'])
            </div>
            <div class="media-stack">
                @foreach(($material['community_images'] ?? []) as $img)
                    <img src="{{ $img }}" alt="Core Four Roofing in the community">
                @endforeach
            </div>
        </div>
    </div>
</section>

@include('partials.reviews')
@include('partials.coverage')
@endsection
