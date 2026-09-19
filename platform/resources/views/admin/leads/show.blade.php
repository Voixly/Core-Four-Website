@extends('layouts.admin')
@section('title', $lead->name)
@section('content')
<div class="grid" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
    <div class="panel">
        <p>{{ $lead->type }} · {{ $lead->source }} · {{ $lead->city }} {{ $lead->zip }}</p>
        <p>@if($lead->phone)<a class="btn" href="tel:{{ $lead->phone }}">Call {{ $lead->phone }}</a>@endif</p>
        <p>{{ $lead->email }}</p>
        <p>{{ $lead->need }}</p>
        <p>{{ $lead->notes }}</p>
        <form method="post" action="{{ route('admin.leads.update', $lead) }}">
            @csrf @method('PATCH')
            <label>Status
                <select name="status">
                    @foreach(\App\Models\Lead::STATUSES as $status)
                        <option value="{{ $status }}" @selected($lead->status===$status)>{{ $status }}</option>
                    @endforeach
                </select>
            </label>
            <label>Assign
                <select name="assigned_to">
                    <option value="">Unassigned</option>
                    @foreach($staff as $user)
                        <option value="{{ $user->id }}" @selected($lead->assigned_to===$user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </label>
            <button class="btn" type="submit">Save</button>
        </form>
    </div>
    <div class="panel">
        <h3>Activity</h3>
        @foreach($lead->events as $event)
            <p><strong>{{ $event->event }}</strong> · {{ $event->user?->name }} · {{ $event->created_at }}<br>{{ $event->body }}</p>
        @endforeach
        <form method="post" action="{{ route('admin.leads.note', $lead) }}">
            @csrf
            <textarea name="body" required placeholder="Add a note"></textarea>
            <p><button class="btn" type="submit">Log note</button></p>
        </form>
    </div>
</div>
@endsection
