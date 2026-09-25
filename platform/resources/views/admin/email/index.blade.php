@extends('layouts.admin')
@section('title', 'Email flows')
@section('content')
@if($sequences->isEmpty())
    <div class="panel"><p>No email flows are set up yet.</p></div>
@endif
@foreach($sequences as $sequence)
    <div class="panel">
        <h3>{{ $sequence->name }}</h3>
        <p>{{ $sequence->description }} · {{ $sequence->audience }} · {{ $sequence->is_active ? 'active' : 'paused' }}</p>
        <table>
            <tr><th>#</th><th>Delay</th><th>Subject</th><th></th></tr>
            @foreach($sequence->steps as $step)
                <tr>
                    <td>{{ $step->position }}</td>
                    <td>{{ $step->delay_days }} days</td>
                    <td>{{ $step->subject }}</td>
                    <td>
                        <a href="{{ route('admin.email.edit', $step) }}">Edit</a>
                        · <a href="{{ route('admin.email.preview', $step) }}" target="_blank">Preview</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endforeach
@endsection
