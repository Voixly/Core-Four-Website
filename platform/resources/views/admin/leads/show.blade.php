@extends('layouts.admin')
@section('title', $lead->name)
@section('content')
<div class="grid" style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:1rem">
    <div class="panel">
        @if($lead->source === 'hiring')
            <p>Hiring application · {{ $lead->need }} · {{ $lead->city }} {{ $lead->zip }}</p>
        @else
            <p>{{ $lead->type }} · {{ $lead->source }} · {{ $lead->city }} {{ $lead->zip }}</p>
        @endif
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
        @unless($lead->source === 'hiring')
        <form method="post" action="{{ route('admin.reviews.store') }}" style="margin-top:1rem">
            @csrf
            <input type="hidden" name="lead_id" value="{{ $lead->id }}">
            <label class="remember"><input type="checkbox" name="send_email" value="1" style="width:auto"> Email the customer</label>
            <p><button class="btn btn-ghost" type="submit">Ask for a Review Shield rating</button></p>
        </form>
        @if($lead->job)
            <p style="margin-top:1rem"><a class="btn" href="{{ route('admin.jobs.show', $lead->job) }}">Open job {{ $lead->job->number }}</a></p>
        @else
            <form method="post" action="{{ route('admin.leads.jobs.store', $lead) }}" style="margin-top:1rem">
                @csrf
                <h3>Create job</h3>
                <label>Pipeline
                    <select name="pipeline_id" required>
                        @foreach($pipelines as $pipeline)
                            <option value="{{ $pipeline->id }}" @selected($suggested?->id === $pipeline->id)>{{ $pipeline->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Payment
                    <select name="payment_path">
                        <option value="retail">Retail</option>
                        <option value="insurance" @selected(str_contains($lead->source.$lead->need.$lead->page_url, 'insurance') || str_contains($lead->source.$lead->need.$lead->page_url, 'storm'))>Insurance</option>
                        <option value="financing">Financing</option>
                        <option value="commercial_capex">Commercial CapEx</option>
                    </select>
                </label>
                <label>Urgency
                    <select name="urgency">
                        <option value="standard">Standard</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </label>
                <label>Address <input name="address"></label>
                <label>City <input name="city" value="{{ $lead->city }}"></label>
                <label>ZIP <input name="zip" value="{{ $lead->zip }}"></label>
                <input type="hidden" name="assigned_to" value="{{ $lead->assigned_to }}">
                <p><button class="btn" type="submit">Open job on this pipeline</button></p>
            </form>
        @endif
        @endunless
        <details class="spam-delete">
            <summary>Delete spam lead</summary>
            <form method="post" action="{{ route('admin.leads.destroy', $lead) }}" onsubmit="return confirm({{ json_encode('Delete '.$lead->name.' now? This cannot be undone.', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_THROW_ON_ERROR) }})">
                @csrf
                @method('delete')
                <p>This removes the lead, the notes, and any emails still waiting to send.@if($lead->job) Job {{ $lead->job->number }} stays.@endif</p>
                <label class="remember"><input type="checkbox" name="spam" value="1" required> This lead is spam</label>
                <label>Type DELETE
                    <input name="confirm_word" required autocomplete="off" spellcheck="false" value="{{ old('confirm_word') }}">
                </label>
                <button class="btn" type="submit">Delete this lead</button>
            </form>
        </details>
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
