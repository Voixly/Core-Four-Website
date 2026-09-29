@extends('layouts.public')
@section('robots', 'noindex, nofollow')
@section('title', 'Prospect intake')
@section('description', 'Staff intake for Greater Houston roofing prospects.')
@section('content')
<section class="review-hero">
    <div class="wrap review-wrap">
        <p class="kicker">Internal</p>
        <h1>Add a roofing prospect</h1>
        <p class="review-lede">Residential prospects join the homeowner outreach. Commercial prospects join the building outreach. Commercial coatings prospects join a coatings-only outreach. These are separate from the guide and inspection emails.</p>
    </div>
</section>
<section class="section section--tight">
    <div class="wrap review-wrap">
        <div class="form-card">
            @if(!empty($saved))
                <p class="flash">{{ $saved }}</p>
            @endif
            @if($errors->any())
                <p class="flash is-error">{{ $errors->first() }}</p>
            @endif
            <form class="lead-form" method="post" action="{{ route('prospects.store') }}">
                @csrf
                <label>Name
                    <input name="name" required autocomplete="name" value="{{ old('name') }}">
                </label>
                <label>Email
                    <input name="email" type="email" required autocomplete="email" value="{{ old('email') }}">
                </label>
                <label>Phone
                    <input name="phone" autocomplete="tel" value="{{ old('phone') }}">
                </label>
                <label>City
                    <input name="city" value="{{ old('city') }}" placeholder="Greater Houston">
                </label>
                <label>Type
                    <select name="type" required>
                        <option value="residential" @selected(old('type', 'residential') === 'residential')>Residential</option>
                        <option value="commercial" @selected(old('type') === 'commercial')>Commercial</option>
                        <option value="coatings" @selected(old('type') === 'coatings')>Commercial coatings</option>
                    </select>
                </label>
                <label>Note
                    <textarea name="notes" rows="4">{{ old('notes') }}</textarea>
                </label>
                <button class="btn" type="submit">Add prospect</button>
            </form>
        </div>
    </div>
</section>
@endsection
