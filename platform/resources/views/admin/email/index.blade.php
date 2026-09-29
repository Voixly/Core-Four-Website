@extends('layouts.admin')
@section('title', 'Email flows')
@section('content')
@if($sequences->isEmpty())
    <div class="panel"><p>No email flows are set up yet.</p></div>
@endif
@foreach([
    '12-month nurtures' => $sequences->filter(fn ($sequence) => ! str_contains(strtolower($sequence->name), 'prospect')),
    'Prospect outreach' => $sequences->filter(fn ($sequence) => str_contains(strtolower($sequence->name), 'prospect')),
] as $heading => $group)
    @if($group->isNotEmpty())
        <h2>{{ $heading }}</h2>
        @foreach($group as $sequence)
            <div class="panel" id="flow-{{ $sequence->id }}">
                <h3>{{ $sequence->name }}</h3>
                <p>{{ $sequence->description }} · {{ $sequence->audience }} · {{ $sequence->is_active ? 'active' : 'paused' }}</p>
                @foreach($sequence->steps as $step)
                    <article class="email-step">
                        <p class="email-step-meta">Day {{ $step->delay_days }} · {{ $step->is_active ? 'active' : 'paused' }}</p>
                        <h4>{{ $step->subject }}</h4>
                        <div class="email-step-body">{{ $step->body }}</div>
                        <p>
                            <a class="btn" href="{{ route('admin.email.edit', $step) }}">Edit</a>
                            <a href="{{ route('admin.email.preview', $step) }}" target="_blank">Preview</a>
                        </p>
                    </article>
                @endforeach
            </div>
        @endforeach
    @endif
@endforeach
@endsection
