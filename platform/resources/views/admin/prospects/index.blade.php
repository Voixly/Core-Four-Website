@extends('layouts.admin')
@section('title', 'Prospects')
@section('content')
<p>These people are on the outreach drip. They move to Leads when they respond.</p>
<form class="filters" method="get">
    <input name="q" value="{{ request('q') }}" placeholder="Search name, phone, city">
    <select name="type">
        <option value="">All</option>
        <option value="residential" @selected(request('type')==='residential')>Residential</option>
        <option value="commercial" @selected(request('type')==='commercial')>Commercial</option>
        <option value="coatings" @selected(request('type')==='coatings')>Commercial coatings</option>
    </select>
    <button class="btn" type="submit">Filter</button>
</form>
@if($prospects->isEmpty())
    <div class="panel"><p>No prospects yet.</p></div>
@endif
<table>
    <tr><th>Name</th><th>Email</th><th>City</th><th>Type</th><th></th></tr>
    @foreach($prospects as $prospect)
        <tr>
            <td><a href="{{ route('admin.leads.show', $prospect) }}">{{ $prospect->name }}</a></td>
            <td>{{ $prospect->email }}</td>
            <td>{{ $prospect->city }}</td>
            <td>{{ $prospect->typeLabel() }}</td>
            <td>
                <form method="post" action="{{ route('admin.prospects.respond', $prospect) }}">
                    @csrf
                    <button class="btn" type="submit">They responded</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
{{ $prospects->links() }}
@endsection
