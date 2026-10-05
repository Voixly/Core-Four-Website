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
                    @if(in_array($key, ['office_address', 'hours', 'notify_emails', 'chat_offline', 'google_review_url', 'yelp_review_url'], true))
                        <textarea name="{{ $key }}" rows="3">{{ $value }}</textarea>
                    @else
                        <input name="{{ $key }}" value="{{ $value }}">
                    @endif
                </label>
            @endif
        @endforeach
        <button class="btn" type="submit">Save settings</button>
    </form>
</div>
@endsection
