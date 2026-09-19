@extends('layouts.admin')
@section('title', 'Settings')
@section('content')
<div class="panel">
    <form method="post" action="{{ route('admin.settings.update') }}">
        @csrf
        @foreach($keys as $key => $value)
            @if($key === 'mail_from')
                <p>Mail from address is set in <code>.env</code>: {{ $value }}</p>
            @else
                <label>{{ str_replace('_', ' ', $key) }}
                    <input name="{{ $key }}" value="{{ $value }}">
                </label>
            @endif
        @endforeach
        <button class="btn" type="submit">Save settings</button>
    </form>
</div>
@endsection
