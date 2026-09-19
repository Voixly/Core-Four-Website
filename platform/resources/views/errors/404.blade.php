@extends('layouts.public')
@section('title', 'Page not found | Core Four Roofing')
@section('content')
<section class="section">
    <div class="wrap">
        <h1>That page is gone</h1>
        <p>If you followed an old WordPress link, try the hubs below or call {{ $officePhone }}.</p>
        <p>
            <a class="btn" href="{{ url('/residential-roofing/') }}">Residential</a>
            <a class="btn" href="{{ url('/commercial-roofing/') }}">Commercial</a>
            <a class="btn" href="{{ url('/contact-core-four-roofing/') }}">Contact</a>
        </p>
    </div>
</section>
@endsection
