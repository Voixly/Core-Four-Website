@extends('layouts.public')
@section('title', 'Roofing Guides | Core Four Roofing')
@section('content')
<section class="section">
    <div class="wrap">
        <h1>Guides for Houston homeowners and property managers</h1>
        <p>Download one. We email the file and start the matching 12-month nurture — unsubscribe anytime.</p>
        <div class="grid-2">
            @foreach($guides as $guide)
                <a class="card" href="{{ url('/guides/'.$guide->slug) }}">
                    <p class="kicker">{{ $guide->audience }}</p>
                    <h3>{{ $guide->title }}</h3>
                    <p>{{ $guide->excerpt }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
