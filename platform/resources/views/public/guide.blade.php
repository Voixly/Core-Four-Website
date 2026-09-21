@extends('layouts.public')
@section('title', $guide->title.' | Core Four Roofing')
@section('audience', $guide->audience === 'commercial' ? 'commercial' : 'residential')
@section('description', $guide->excerpt)
@section('content')
<section class="review-hero">
    <div class="wrap review-wrap">
        <p class="kicker">{{ $guide->audience }} guide</p>
        <h1>{{ $guide->title }}</h1>
        <p class="review-lede">{{ $guide->excerpt }}</p>
    </div>
</section>
<section class="section section--tight">
    <div class="wrap review-wrap">
        <div class="form-card">
            <h3>Email me the PDF</h3>
            @include('partials.guide-form', ['guide' => $guide])
        </div>
    </div>
</section>
@endsection
