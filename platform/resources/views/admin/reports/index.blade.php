@extends('layouts.admin')
@section('title', 'Reports')
@section('content')
@if($reports->isEmpty())
    <div class="panel"><p>No reports are set up yet.</p></div>
@endif
<div class="grid-3" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem">
    @foreach($reports as $report)
        <a class="panel" href="{{ route('admin.reports.show', $report->slug) }}" style="text-decoration:none;color:inherit">
            <h3>{{ $report->title }}</h3>
            <p>{{ $report->description }}</p>
        </a>
    @endforeach
</div>
@endsection
