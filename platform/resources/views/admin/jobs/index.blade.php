@extends('layouts.admin')
@section('title', 'Jobs')
@section('meta', $pipeline ? 'Drag a card to another column · '.$pipeline->name : 'Job board')
@section('actions')
    <a class="btn" href="{{ route('admin.jobs.create') }}">New job</a>
@endsection
@section('content')
@if(! $pipeline)
    <div class="panel"><p>No job pipelines are set up yet.</p></div>
@else
<form class="filters" method="get">
    <select name="pipeline" onchange="this.form.submit()">
        @foreach($pipelines as $option)
            <option value="{{ $option->slug }}" @selected($pipeline->id === $option->id)>{{ $option->name }}</option>
        @endforeach
    </select>
    <select name="status">
        <option value="">All open + closed</option>
        @foreach(\App\Models\Job::STATUSES as $status)
            <option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
        @endforeach
    </select>
    <input name="q" value="{{ request('q') }}" placeholder="Search number, name, city">
    <button class="btn" type="submit">Filter</button>
</form>
<div class="kanban" data-kanban>
    @foreach($pipeline->stages as $stage)
        <section class="kanban-col" data-stage-id="{{ $stage->id }}">
            <header>
                <strong>{{ $stage->name }}</strong>
                <span data-count>{{ ($jobsByStage[$stage->id] ?? collect())->count() }}</span>
            </header>
            <div class="kanban-list">
                @foreach($jobsByStage[$stage->id] ?? [] as $job)
                    <article
                        class="kanban-card"
                        data-job-id="{{ $job->id }}"
                        data-move="{{ route('admin.jobs.move', $job) }}"
                    >
                        <div class="kanban-card-top">
                            <span class="kanban-grip" aria-hidden="true"><i class="fas fa-grip-vertical"></i></span>
                            <a href="{{ route('admin.jobs.show', $job) }}"><strong>{{ $job->title() }}</strong></a>
                        </div>
                        <div class="page-meta">{{ $job->number }} · {{ $job->city }} · {{ $job->urgency }}</div>
                        @if($job->nextRequestedDoc())
                            <div class="page-meta">Need: {{ $job->nextRequestedDoc()->label }}</div>
                        @endif
                        <form method="post" action="{{ route('admin.jobs.move', $job) }}">
                            @csrf
                            <select name="stage_id" onchange="this.form.submit()">
                                @foreach($pipeline->stages as $option)
                                    <option value="{{ $option->id }}" @selected($option->id === $job->stage_id)>{{ $option->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach
</div>
@endif
@endsection
@push('scripts')
<script src="@assetv('/js/admin-kanban.js')"></script>
@endpush
