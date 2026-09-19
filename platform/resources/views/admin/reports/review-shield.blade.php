@extends('layouts.admin')
@section('title', 'Review Shield')
@section('content')
<div class="panel">
    <p>Private rating first. Nothing auto-posts to Google or Yelp. 4–5★ get an invite link. 1–3★ stay in the recovery queue for a phone call.</p>
</div>
<div class="cards">
    <div class="stat"><span>Trigger</span><b>Job complete in CMS</b></div>
    <div class="stat"><span>Happy path</span><b>4–5★ → Google / Yelp</b></div>
    <div class="stat"><span>Recovery</span><b>1–3★ held</b></div>
    <div class="stat"><span>Automation</span><b>Later add-on</b></div>
</div>
<div class="panel">
    <h3>How it works</h3>
    <ol>
        <li>Superintendent marks the job complete.</li>
        <li>Homeowner gets a private 1–5 rating (SMS/email later; this page is the plan).</li>
        <li>Happy: one-tap invite to Google Business Profile and Yelp.</li>
        <li>Unhappy: office gets a task. Fix the flashing, not the internet.</li>
    </ol>
    <p>v1 ships this plan inside Reports. Live SMS Review Shield is out of scope until the CMS trigger exists.</p>
</div>
@endsection
