@extends('layouts.public')
@section('title', $guide->title.' | Core Four Roofing')
@section('audience', $guide->audience === 'commercial' ? 'commercial' : 'residential')
@section('content')
<section class="section">
    <div class="wrap grid-2">
        <div>
            <p class="kicker">{{ $guide->audience }} guide</p>
            <h1>{{ $guide->title }}</h1>
            <p>{{ $guide->excerpt }}</p>
        </div>
        <div class="form-card">
            <h3>Send me the guide</h3>
            <form method="post" action="{{ route('guides.download', $guide->slug) }}">
                @csrf
                <label>Name <input name="name" required></label>
                <label>Email <input name="email" type="email" required></label>
                <label>Phone <input name="phone" required></label>
                <label>ZIP <input name="zip"></label>
                <input type="hidden" name="type" value="{{ $guide->audience === 'commercial' ? 'commercial' : 'residential' }}">
                <button class="btn btn-wide" type="submit">Email me the PDF</button>
            </form>
        </div>
    </div>
</section>
@endsection
