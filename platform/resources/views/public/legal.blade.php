@extends('layouts.public')
@section('title', $title)
@section('content')
<section class="section">
    <div class="wrap" style="max-width:720px">
        <h1>{{ $heading }}</h1>
        <p>Core Four Roofing, 22955 State Highway 249 Suite 26, Tomball, TX 77375, {{ $officePhone }}.</p>
        <p>We collect name, phone, email, ZIP, and chat messages so we can inspect, bid, and follow up. We do not sell your list. Emails include unsubscribe. Chat transcripts may become a lead record for the office.</p>
        <p>Guides and nurture sequences are optional. You can ask us to delete a lead by emailing the office.</p>
    </div>
</section>
@endsection
