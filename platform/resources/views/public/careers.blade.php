@extends('layouts.public')
@section('title', 'Careers | Core Four Roofing')
@section('description', 'Crew, service, estimator, and office jobs at Core Four Roofing in Tomball. Apply from the careers page.')
@section('content')
<section class="review-hero">
    <div class="wrap review-wrap">
        <p class="kicker">Careers</p>
        <h1>Work with Core Four Roofing</h1>
        <p class="review-lede">Crew, service, and office roles out of our Tomball shop. We hire people who show up and do the work in front of the customer.</p>
        <p><a class="btn btn--white" href="#apply">Apply</a></p>
    </div>
</section>
<section class="section">
    <div class="wrap grid-2">
        <div>
            <h2>Open roles</h2>
            <p>The work is residential and commercial roofs across the Houston area, plus scheduled jobs in Austin and Dallas–Fort Worth. The shop is at 22955 State Highway 249, Tomball.</p>
            <ul class="careers-roles">
                <li><strong>Roofing installer.</strong> Tear-off, install, and cleanup on the job.</li>
                <li><strong>Service technician.</strong> Leaks, repairs, and follow-up after a storm.</li>
                <li><strong>Estimator.</strong> Inspect the roof, write the scope, and walk the customer through it.</li>
                <li><strong>Office.</strong> Phones, scheduling, and paperwork at the shop.</li>
            </ul>
            <p>Send the form. The office reads every application and calls people we want to meet.</p>
        </div>
        <div class="form-card" id="apply">
            <h3>Apply</h3>
            <p>Tell us the role and where you’ve worked. We’ll call from the Tomball office.</p>
            @include('partials.hiring-form')
        </div>
    </div>
</section>
@endsection
