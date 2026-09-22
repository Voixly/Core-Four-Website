@extends('layouts.admin')
@section('title', 'Leads')
@section('content')
<form class="filters" method="get">
    <input name="q" value="{{ request('q') }}" placeholder="Search name, phone, city">
    <select name="status">
        <option value="">All statuses</option>
        @foreach(\App\Models\Lead::STATUSES as $status)
            <option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
        @endforeach
    </select>
    <select name="type">
        <option value="">Res + comm</option>
        <option value="residential" @selected(request('type')==='residential')>Residential</option>
        <option value="commercial" @selected(request('type')==='commercial')>Commercial</option>
    </select>
    <select name="source">
        <option value="">All sources</option>
        <option value="hiring" @selected(request('source')==='hiring')>Hiring</option>
    </select>
    <button class="btn" type="submit">Filter</button>
</form>
<table>
    <tr><th>Name</th><th>Phone</th><th>City</th><th>Type</th><th>Source</th><th>Status</th><th>Owner</th></tr>
    @foreach($leads as $lead)
        <tr>
            <td><a href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a></td>
            <td>@if($lead->phone)<a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>@endif</td>
            <td>{{ $lead->city }}</td>
            <td>{{ $lead->source === 'hiring' ? 'Hiring' : $lead->type }}</td>
            <td>{{ $lead->source }}</td>
            <td><span class="tag tag-{{ $lead->status }}">{{ $lead->status }}</span></td>
            <td>{{ $lead->assignee?->name }}</td>
        </tr>
    @endforeach
</table>
{{ $leads->links() }}
@endsection
