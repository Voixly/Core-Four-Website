@extends('layouts.admin')
@section('title', $sequence->name)
@section('content')
<p><a href="{{ route('admin.email.index') }}">All email flows</a></p>
<div class="panel">
    <h3>{{ $sequence->name }}</h3>
    <p>{{ $sequence->description }} · {{ $sequence->audience }} · {{ $sequence->is_active ? 'active' : 'paused' }}</p>
    @forelse($sequence->steps as $step)
        <article class="email-step" id="step-{{ $step->id }}">
            <p class="email-step-meta">Day {{ $step->delay_days }} · {{ $step->is_active ? 'active' : 'paused' }}</p>
            <h4>{{ $step->subject }}</h4>
            <div class="email-step-body">{{ $step->body }}</div>
            <p>
                <a class="btn" href="{{ route('admin.email.edit', $step) }}">Edit</a>
                <a href="{{ route('admin.email.preview', $step) }}" target="_blank">Preview</a>
            </p>
        </article>
    @empty
        <p>This flow has no emails yet.</p>
    @endforelse
</div>
@endsection
