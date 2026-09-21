@extends('layouts.public')
@section('title', 'How did we do? | Core Four Roofing')
@section('description', 'Tell Core Four how the job went. Ten seconds, private first. If something went wrong, a manager calls before anything is public.')
@section('content')
<section class="review-hero">
    <div class="wrap review-wrap">
        <p class="kicker">Review Shield</p>
        <h1>How did we do?</h1>
        <p class="review-lede">This stays between you and Core Four first. It is not posted to Google or Yelp. Pick a star rating — ten seconds — and we’ll take the next step.</p>
    </div>
</section>
<section class="section section--tight">
    <div class="wrap review-wrap">
        <form class="review-card" method="post" action="{{ $review ? route('reviews.store.token', $review->token) : route('reviews.store') }}">
            @csrf
            @if($errors->any())
                <p class="flash is-error">{{ $errors->first() }}</p>
            @endif
            <fieldset class="star-picker">
                <legend>Your private rating</legend>
                @for($i = 1; $i <= 5; $i++)
                    <label class="star-option">
                        <input type="radio" name="stars" value="{{ $i }}" required>
                        <span class="star-face">
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <em>{{ $i }}</em>
                        </span>
                    </label>
                @endfor
            </fieldset>
            <div class="review-fields">
                <label>Name
                    <input name="name" value="{{ old('name', $review->name ?? '') }}" autocomplete="name">
                </label>
                <label>Phone
                    <input name="phone" value="{{ old('phone', $review->phone ?? '') }}" autocomplete="tel">
                </label>
                <label>Email
                    <input type="email" name="email" value="{{ old('email', $review->email ?? '') }}" autocomplete="email">
                </label>
                <label>City
                    <input name="city" value="{{ old('city', $review->city ?? '') }}">
                </label>
                <label>Job type
                    <select name="type">
                        <option value="residential" @selected(old('type', $review->type ?? 'residential')==='residential')>Residential</option>
                        <option value="commercial" @selected(old('type', $review->type ?? '')==='commercial')>Commercial</option>
                    </select>
                </label>
                <label class="review-comment">Anything we should know?
                    <textarea name="comment" placeholder="Optional — especially if something felt off.">{{ old('comment') }}</textarea>
                </label>
            </div>
            <button class="btn" type="submit">Submit private rating <i class="fas fa-arrow-right"></i></button>
            <p class="review-fine">4–5 stars: we’ll invite you to share that on Google or Yelp. 1–3 stars: a manager calls you. We never post a review for you.</p>
        </form>
    </div>
</section>
@endsection
