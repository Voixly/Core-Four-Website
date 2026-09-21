@extends('layouts.admin')
@section('title', ($review->name ?: 'Review').' · Review Shield')
@section('content')
<div class="grid" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
    <div class="panel">
        <p><span class="tag tag-{{ $review->status }}">{{ $review->status }}</span> · {{ $review->stars ? $review->stars.'★' : 'not rated' }} · {{ $review->type }}</p>
        <p><strong>{{ $review->name ?: 'Unnamed customer' }}</strong><br>
            @if($review->phone)<a href="tel:{{ $review->phone }}">{{ $review->phone }}</a><br>@endif
            {{ $review->email }}<br>
            {{ $review->city }}<br>
            {{ $review->job }}
        </p>
        @if($review->comment)
            <p>{{ $review->comment }}</p>
        @endif
        <p>
            @if($review->phone)<a class="btn" href="tel:{{ $review->phone }}">Call</a>@endif
            @if($review->lead_id)<a class="btn btn-ghost" href="{{ route('admin.leads.show', $review->lead_id) }}">Open lead</a>@endif
        </p>
        <label>Customer link
            <input readonly value="{{ $review->publicUrl() }}" onclick="this.select()">
        </label>
        <p class="page-meta">Share this after a job is complete. First screen is stars — not Google.</p>
        @if($review->google_clicked_at)<p>Clicked Google {{ $review->google_clicked_at->timezone(config('app.timezone')) }}</p>@endif
        @if($review->yelp_clicked_at)<p>Clicked Yelp {{ $review->yelp_clicked_at->timezone(config('app.timezone')) }}</p>@endif
    </div>
    <div class="panel">
        <h3>Office follow-up</h3>
        <form method="post" action="{{ route('admin.reviews.update', $review) }}">
            @csrf @method('PATCH')
            <label>Status
                <select name="status">
                    @foreach(\App\Models\Review::STATUSES as $status)
                        <option value="{{ $status }}" @selected($review->status===$status)>{{ $status }}</option>
                    @endforeach
                </select>
            </label>
            <label>Assign
                <select name="assigned_to">
                    <option value="">Unassigned</option>
                    @foreach($staff as $user)
                        <option value="{{ $user->id }}" @selected($review->assigned_to===$user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Recovery notes
                <textarea name="recovery_notes" rows="5">{{ $review->recovery_notes }}</textarea>
            </label>
            <button class="btn" type="submit">Save</button>
        </form>
    </div>
</div>
@endsection
