@extends('layouts.admin')
@section('title', 'Schedule')
@section('meta', $start->format('M j').' – '.$end->format('M j, Y'))
@section('actions')
    <a class="btn btn-ghost" href="{{ route('admin.schedule', ['week' => $prev]) }}">Previous week</a>
    <a class="btn btn-ghost" href="{{ route('admin.schedule') }}">This week</a>
    <a class="btn" href="{{ route('admin.schedule', ['week' => $next]) }}">Next week</a>
@endsection
@section('content')
<div class="cal-week">
    @foreach($days as $day)
        @php $key = $day->toDateString(); @endphp
        <section class="cal-day {{ $day->isToday() ? 'is-today' : '' }}">
            <header>
                <strong>{{ $day->format('D') }}</strong>
                <span>{{ $day->format('M j') }}</span>
            </header>
            @forelse($appointments[$key] ?? [] as $appointment)
                <a class="cal-item" href="{{ route('admin.jobs.show', ['job' => $appointment->job, 'tab' => 'schedule']) }}">
                    <b>{{ $appointment->starts_at->timezone(config('app.timezone'))->format('g:ia') }}</b>
                    <span>{{ $appointment->label() }}</span>
                    <small>{{ $appointment->job->number }} · {{ $appointment->job->title() }}</small>
                    @if($appointment->assignee)<small>{{ $appointment->assignee->name }}</small>@endif
                </a>
            @empty
                <p class="page-meta">Open</p>
            @endforelse
        </section>
    @endforeach
</div>
@endsection
