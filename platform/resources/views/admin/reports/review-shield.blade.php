@extends('layouts.admin')
@section('title', 'Review Shield plan')
@section('content')
<div class="panel">
    <p>The live tool is in the sidebar now. This page is the original plan.</p>
    <p><a class="btn" href="{{ route('admin.reviews.index') }}">Open Review Shield queue</a>
       <a class="btn btn-ghost" href="{{ url('/reviews/') }}" target="_blank">Public rating page</a></p>
</div>
<div class="cards">
    <div class="stat"><span>Trigger</span><b>Job complete / public page</b></div>
    <div class="stat"><span>Happy path</span><b>4–5★ → Google / Yelp</b></div>
    <div class="stat"><span>Recovery</span><b>1–3★ held</b></div>
    <div class="stat"><span>Posting</span><b>Never by us</b></div>
</div>
<div class="panel">
    <h3>How it works</h3>
    <ol>
        <li>Office sends a Review Shield link after a job, or the customer uses /reviews/.</li>
        <li>First screen is a private 1–5 rating — not Google.</li>
        <li>4–5★: invite buttons for the Tomball Google listing and Yelp. Customer writes it.</li>
        <li>1–3★: Google and Yelp stay hidden. Office gets a hold ticket and calls {{ $officePhone }}.</li>
    </ol>
</div>
@endsection
