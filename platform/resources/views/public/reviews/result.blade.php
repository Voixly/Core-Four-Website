@extends('layouts.public')
@section('title', ($review->isHappy() ? 'Share your review' : 'We’ll make this right').' | Core Four Roofing')
@section('description', 'Thanks for rating Core Four Roofing. This page is for the crew that just finished your job.')
@section('robots', 'noindex, nofollow')
@section('content')
<section class="review-hero">
    <div class="wrap review-wrap">
        @if($review->isHappy())
            <p class="kicker">Thank you</p>
            <h1>Glad the job landed right.</h1>
            <p class="review-lede">You rated us {{ $review->stars }} stars. If you have a minute, share that on Google or Yelp — you write it, we never post for you.</p>
        @else
            <p class="kicker">We heard you</p>
            <h1>A manager will call you.</h1>
            <p class="review-lede">You rated us {{ $review->stars }} stars. Google and Yelp stay hidden. We want the chance to fix it in-house first. If water is coming in now, call {{ $officePhone }}.</p>
        @endif
    </div>
</section>
<section class="section section--tight">
    <div class="wrap review-wrap">
        <div class="review-card">
            @if($review->isHappy())
                <div class="review-dests">
                    <a class="btn" href="{{ $googleUrl }}" rel="noopener">Leave a Google review <i class="fas fa-arrow-right"></i></a>
                    <a class="btn btn--ghost" href="{{ $yelpUrl }}" rel="noopener">Leave a Yelp review</a>
                </div>
            @else
                <p>If we have your number, the Tomball office will reach out today. You can also call us now.</p>
                <p><a class="btn" href="tel:+1{{ $officePhoneTel }}">Call {{ $officePhone }}</a></p>
            @endif
            @if($review->comment)
                <p class="review-fine">You wrote: “{{ $review->comment }}”</p>
            @endif
        </div>
    </div>
</section>
@endsection
