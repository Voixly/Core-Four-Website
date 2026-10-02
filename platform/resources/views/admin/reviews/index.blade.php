@extends('layouts.admin')
@section('title', 'Review Shield')
@section('content')
<div class="cards">
    <div class="stat"><span>Held 1–3★</span><b>{{ $held }}</b></div>
    <div class="stat"><span>Invited 4–5★</span><b>{{ $invited }}</b></div>
    <div class="stat"><span>Waiting on rating</span><b>{{ $pending }}</b></div>
    <div class="stat"><span>Clicked Google</span><b>{{ $googleClicks }}</b></div>
</div>
<div class="panel">
    <p>Private rating first. Nothing auto-posts. 4–5★ get Google / Yelp. 1–3★ stay here for a same-day callback.</p>
    <p><a href="{{ url('/reviews/') }}" target="_blank">Open public review page</a></p>
</div>
<div class="panel" id="review-emails">
    <h3>The review emails</h3>
    <p>Customers get these in order. The first goes when you send the link. The later ones follow on their own and stop if the customer rates.</p>
    @forelse($reviewSteps as $step)
        <article class="email-step" id="review-email-{{ $step->id }}">
            <p class="email-step-meta">Email {{ $loop->iteration }} of {{ $reviewSteps->count() }} · Day {{ $step->delay_days }} · {{ $step->is_active ? 'active' : 'paused' }}</p>
            <h4>{{ $step->subject }}</h4>
            <div class="email-step-body">{{ $step->body }}</div>
            <p>
                <a class="btn" href="{{ route('admin.email.edit', $step) }}">Edit</a>
                <a href="{{ route('admin.email.preview', $step) }}" target="_blank">Preview</a>
            </p>
        </article>
    @empty
        <p>The review emails are not set up yet.</p>
    @endforelse
</div>
<div class="panel">
    <h3>Rating emails</h3>
    <p>{{ $mailsSent }} {{ $mailsSent === 1 ? 'email has' : 'emails have' }} been sent to ask for a review.@if($mailsFailed) {{ $mailsFailed }} did not send.@endif The first note goes when you check the box, or when you send everyone who is still waiting. Three more follow, and they stop if the customer rates. <a href="{{ route('admin.email.index') }}">Open the review flow</a></p>
    <form method="post" action="{{ route('admin.reviews.send-pending') }}" onsubmit="return confirm('Send the first rating note to every waiting customer who has an email and has not been emailed yet?')">
        @csrf
        <button class="btn" type="submit">Send all pending{{ $pendingUnsent ? ' ('.$pendingUnsent.')' : '' }}</button>
    </form>
    @if($mailLog->isEmpty())
        <p>No rating emails yet. Check “Email them the link” when you create an invite.</p>
    @else
        <table>
            <tr><th>When</th><th>To</th><th>Subject</th><th>Result</th></tr>
            @foreach($mailLog as $entry)
                <tr>
                    <td>{{ optional($entry->sent_at ?? $entry->scheduled_at)->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</td>
                    <td>
                        @if($entry->review)
                            <a href="{{ route('admin.reviews.show', $entry->review) }}">{{ $entry->review->name ?: $entry->email }}</a>
                        @else
                            {{ $entry->email }}
                        @endif
                    </td>
                    <td>{{ $entry->subject }}</td>
                    <td>{{ $entry->resultLabel() }}</td>
                </tr>
            @endforeach
        </table>
    @endif
</div>
<div class="panel">
    <h3>Completed JobNimbus jobs</h3>
    <p>Pull finished jobs into this list. Nothing is emailed until you send the review link.</p>
    <form method="post" action="{{ route('admin.reviews.jobnimbus') }}">
        @csrf
        <button class="btn" type="submit">Sync completed jobs</button>
    </form>
</div>
<div class="panel">
    <h3>Create an invite</h3>
    <form class="filters" method="post" action="{{ route('admin.reviews.store') }}">
        @csrf
        <input name="name" placeholder="Customer name">
        <input name="phone" placeholder="Phone">
        <input type="email" name="email" placeholder="Email">
        <input name="city" placeholder="City">
        <input name="job" placeholder="Job / address">
        <select name="type">
            <option value="residential">Residential</option>
            <option value="commercial">Commercial</option>
        </select>
        <label class="remember"><input type="checkbox" name="send_email" value="1" style="width:auto"> Email them the link</label>
        <button class="btn" type="submit">Make Review Shield link</button>
    </form>
</div>
<form class="filters" method="get">
    <input name="q" value="{{ request('q') }}" placeholder="Search name, city, job">
    <select name="status">
        <option value="">All statuses</option>
        @foreach(\App\Models\Review::STATUSES as $status)
            <option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
        @endforeach
    </select>
    <button class="btn" type="submit">Filter</button>
</form>
<table>
    <tr>
        <th>Customer</th>
        <th>Stars</th>
        <th>City</th>
        <th>Status</th>
        <th>Public</th>
        <th></th>
    </tr>
    @foreach($reviews as $review)
        <tr>
            <td>
                <a href="{{ route('admin.reviews.show', $review) }}">{{ $review->name ?: 'Unnamed' }}</a>
                <div class="page-meta">{{ $review->job ?: $review->type }}</div>
            </td>
            <td>{{ $review->stars ? $review->stars.'★' : '—' }}</td>
            <td>{{ $review->city }}</td>
            <td><span class="tag tag-{{ $review->status }}">{{ $review->status }}</span></td>
            <td>
                @if($review->google_clicked_at) Google @endif
                @if($review->yelp_clicked_at) Yelp @endif
                @if(! $review->google_clicked_at && ! $review->yelp_clicked_at) — @endif
            </td>
            <td><a href="{{ route('admin.reviews.show', $review) }}">Open</a></td>
        </tr>
    @endforeach
</table>
{{ $reviews->links() }}
@endsection
