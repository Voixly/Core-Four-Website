@extends('layouts.account')
@section('title', $job->number)
@section('content')
<p class="kicker">{{ $job->number }} · {{ $job->pipeline?->name }}</p>
<h1>{{ $job->customerVisibleStage()?->customer_label ?: 'Your job is in progress' }}</h1>
@if($job->nextCustomerStage())
    <p class="lead">Next: {{ $job->nextCustomerStage()->customer_label }}</p>
@endif
@if($job->customer_summary)
    <p>{{ $job->customer_summary }}</p>
@endif

<div class="account-stage-rail">
    @foreach($job->pipeline->stages->where('customer_visible', true) as $stage)
        <div class="account-stage {{ $job->stage_id === $stage->id ? 'is-current' : '' }} {{ $stage->sort < ($job->stage?->sort ?? 0) ? 'is-done' : '' }}">
            <b>{{ $stage->customer_label }}</b>
        </div>
    @endforeach
</div>

@if($job->appointments->isNotEmpty())
<section class="account-panel">
    <h2>Upcoming visits</h2>
    @foreach($job->appointments as $appointment)
        <p><strong>{{ $appointment->label() }}</strong><br>{{ $appointment->starts_at->timezone(config('app.timezone'))->format('l, F j \a\t g:ia') }}</p>
    @endforeach
</section>
@endif

@foreach($job->quotes as $quote)
<section class="account-panel">
    <h2>{{ $quote->title }}</h2>
    <p class="page-meta">{{ ucfirst($quote->status) }} · ${{ number_format((float) $quote->total, 2) }}</p>
    @foreach($quote->items as $item)
        <p>{{ $item->label }} · {{ $item->qty }} {{ $item->unit }} · ${{ number_format((float) $item->amount, 2) }}</p>
    @endforeach
    @if($quote->status === 'sent')
        <form method="post" action="{{ route('account.jobs.quotes.accept', [$job, $quote]) }}">
            @csrf
            <button class="btn" type="submit">Approve this estimate</button>
        </form>
    @endif
</section>
@endforeach

<section class="account-panel">
    <h2>Files we need</h2>
    @forelse($job->documentRequests as $request)
        <div class="account-doc">
            <div>
                <strong>{{ $request->label }}</strong>
                <span>{{ $request->isFulfilled() ? 'Received' : ($request->required ? 'Needed' : 'Optional') }}</span>
            </div>
            @unless($request->isFulfilled())
                <form method="post" action="{{ route('account.jobs.documents', $job) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="request_id" value="{{ $request->id }}">
                    <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.heic,.webp">
                    <button class="btn" type="submit">Upload</button>
                </form>
            @endunless
        </div>
    @empty
        <p>No files requested right now.</p>
    @endforelse
    <form class="account-extra-upload" method="post" action="{{ route('account.jobs.documents', $job) }}" enctype="multipart/form-data">
        @csrf
        <label>Add photos or another file
            <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.heic,.webp">
        </label>
        <button class="btn btn--ghost" type="submit">Upload extra file</button>
    </form>
</section>

<section class="account-panel">
    <h2>Shared files</h2>
    @forelse($job->documents as $document)
        <p><a href="{{ route('account.jobs.download', [$job, $document]) }}">{{ $document->original_name }}</a></p>
    @empty
        <p>Nothing shared back yet.</p>
    @endforelse
</section>

@if($job->photos()->isNotEmpty())
<section class="account-panel">
    <h2>Photos</h2>
    @foreach($job->photos() as $document)
        <p><a href="{{ route('account.jobs.download', [$job, $document]) }}">{{ $document->original_name }}</a></p>
    @endforeach
</section>
@endif

@if($job->warranties->isNotEmpty())
<section class="account-panel">
    <h2>Warranties</h2>
    @foreach($job->warranties as $warranty)
        <p>
            <strong>{{ ucfirst($warranty->kind) }}</strong>
            @if($warranty->manufacturer) · {{ $warranty->manufacturer }}@endif
            @if($warranty->expires_on)<br>Through {{ $warranty->expires_on->format('F j, Y') }}@endif
        </p>
    @endforeach
</section>
@endif

<section class="account-panel">
    <h2>Activity</h2>
    @foreach($job->events as $event)
        <p><strong>{{ $event->event }}</strong> · {{ $event->created_at->timezone(config('app.timezone'))->format('M j, g:ia') }}<br>{{ $event->body }}</p>
    @endforeach
</section>
<p><a href="tel:+1{{ $officePhoneTel }}">Call {{ $officePhone }}</a> if water is coming in now.</p>
@endsection
