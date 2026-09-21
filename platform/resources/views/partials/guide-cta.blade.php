@php
    $guides = $guides ?? \App\Models\Guide::featuredFor($context ?? 'home');
@endphp
@if($guides->isNotEmpty())
<section class="guide-cta {{ $guides->count() > 1 ? 'guide-cta--pair' : '' }}">
    <div class="wrap">
        @if($guides->count() === 1)
            @php $guide = $guides->first(); @endphp
            <div class="guide-cta-band">
                <div class="guide-cta-copy">
                    <p class="kicker">Free {{ $guide->audience }} guide</p>
                    <h2>{{ $guide->title }}</h2>
                    <p>{{ $guide->excerpt }}</p>
                </div>
                <a class="btn" href="{{ $guide->path() }}">{{ $guide->ctaLabel() }} <i class="fas fa-arrow-right"></i></a>
            </div>
        @else
            <div class="guide-cta-head">
                <p class="kicker">Free roofing guides</p>
                <h2>Know what to do before you call.</h2>
                <p>Leave your info, we email the PDF. A Tomball estimator can follow up — unsubscribe anytime.</p>
            </div>
            <div class="guide-cta-grid">
                @foreach($guides as $guide)
                    <a class="guide-cta-card" href="{{ $guide->path() }}">
                        <span class="pill pill--green">{{ $guide->audience }}</span>
                        <i class="fas {{ $guide->icon() }}" aria-hidden="true"></i>
                        <h3>{{ $guide->title }}</h3>
                        <p>{{ $guide->excerpt }}</p>
                        <span class="guide-cta-link">{{ $guide->ctaLabel() }} <i class="fas fa-arrow-right"></i></span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif
