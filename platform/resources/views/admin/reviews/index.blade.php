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
