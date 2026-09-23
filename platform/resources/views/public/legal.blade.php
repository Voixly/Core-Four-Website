@extends('layouts.public')
@section('title', $title.' | Core Four Roofing')
@section('description', $description)
@section('content')
<section class="review-hero">
    <div class="wrap review-wrap">
        <h1>{{ $heading }}</h1>
    </div>
</section>
<section class="section">
    <div class="wrap" style="max-width:720px">
        <p>Core Four Roofing, 22955 State Highway 249 Suite 26, Tomball, TX 77375, {{ $officePhone }}.</p>
        @if($heading === 'Privacy Policy')
            <p>When you request an inspection, apply for a job, download a guide, or use the chat, we collect the name, phone, email, address or ZIP, and the message you send. Storm photos and chat transcripts stay with the office so we can call you back and write a scope.</p>
            <p>Job applications go to the hiring desk. Inspection and guide requests go to the office that schedules the work. We do not sell that list. Guide emails include an unsubscribe. To ask us to delete a lead, email hello@corefourroofing.com from the address you used.</p>
            <p>The site uses a session cookie to keep a form from being submitted twice and to run the chat. We do not use that cookie to follow you across other websites.</p>
        @else
            <p>The pages on this site describe how we roof homes and buildings in Texas. They are not a bid, a warranty, or a contract. A price becomes an agreement only when both sides sign a written scope.</p>
            <p>An inspection request tells us you want a look at the roof. It does not reserve a crew or lock a number. What we find on the deck, the flashings, and the attic can change the repair or the replacement we recommend.</p>
            <p>Do not use the forms to send someone else’s information, and do not rely on a blog post as a substitute for a look at your own roof. Questions about a page go to the Tomball office at the phone number above.</p>
        @endif
    </div>
</section>
@endsection
