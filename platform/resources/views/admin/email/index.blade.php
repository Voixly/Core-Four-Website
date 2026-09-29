@extends('layouts.admin')
@section('title', 'Email flows')
@section('content')
<div class="email-flows">
<p>Open a flow to read each email and edit it.</p>
@if($sequences->isEmpty())
    <div class="panel"><p>No email flows are set up yet.</p></div>
@endif
@foreach([
    '12-month nurtures' => $sequences->filter(fn ($sequence) => ! str_contains(strtolower($sequence->name), 'prospect')),
    'Prospect outreach' => $sequences->filter(fn ($sequence) => str_contains(strtolower($sequence->name), 'prospect')),
] as $heading => $group)
    @if($group->isNotEmpty())
        <h2>{{ $heading }}</h2>
        <table>
            <tr><th>Flow</th><th>Emails</th><th></th></tr>
            @foreach($group as $sequence)
                <tr>
                    <td>
                        <a href="{{ route('admin.email.show', $sequence) }}"><strong>{{ $sequence->name }}</strong></a>
                        @if($sequence->description)<br><small>{{ $sequence->description }}</small>@endif
                    </td>
                    <td>{{ $sequence->steps_count }}</td>
                    <td><a class="btn" href="{{ route('admin.email.show', $sequence) }}">Open</a></td>
                </tr>
            @endforeach
        </table>
    @endif
@endforeach
</div>
@endsection
