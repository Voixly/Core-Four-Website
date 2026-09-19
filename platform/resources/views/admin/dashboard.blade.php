@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="cards">
    <div class="stat"><span>New leads</span><b>{{ $newLeads }}</b></div>
    <div class="stat"><span>Open chats</span><b>{{ $openChats }}</b></div>
    <div class="stat"><span>Inspections today</span><b>{{ $inspections }}</b></div>
    <div class="stat"><span>Mail today / queued / failed</span><b>{{ $mailHealth['sent'] }} / {{ $mailHealth['scheduled'] }} / {{ $mailHealth['failed'] }}</b></div>
</div>
<div class="panel">
    <h3>Recent leads</h3>
    <table>
        <tr><th>Name</th><th>Type</th><th>Source</th><th>Status</th><th></th></tr>
        @foreach($recentLeads as $lead)
            <tr>
                <td>{{ $lead->name }}</td>
                <td>{{ $lead->type }}</td>
                <td>{{ $lead->source }}</td>
                <td>{{ $lead->status }}</td>
                <td><a href="{{ route('admin.leads.show', $lead) }}">Open</a></td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
