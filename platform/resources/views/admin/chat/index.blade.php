@extends('layouts.admin')
@section('title', 'Chat inbox')
@section('meta', $counts['waiting'] ? $counts['waiting'].' email '.($counts['waiting'] === 1 ? 'reply needs' : 'replies need').' an answer' : 'Website chats and email replies')
@section('content')
<div class="filters">
    <a class="kind-pill {{ $channel ? '' : 'is-on' }}" href="{{ route('admin.chat.index') }}">All {{ $counts['all'] }}</a>
    <a class="kind-pill is-email {{ $channel === 'email' ? 'is-on' : '' }}" href="{{ route('admin.chat.index', ['channel' => 'email']) }}">Email {{ $counts['email'] }}</a>
    <a class="kind-pill {{ $channel === 'chat' ? 'is-on' : '' }}" href="{{ route('admin.chat.index', ['channel' => 'chat']) }}">Live chat {{ $counts['chat'] }}</a>
</div>
@unless(config('services.resend.webhook_secret'))
    <p class="chat-note">Email replies show up here after the reply domain is connected. Until then, a reply still arrives at hello@corefourroofing.com.</p>
@endunless
@if($conversations->isEmpty())
    <div class="panel"><p>No conversations in this inbox yet.</p></div>
@else
<table>
    <tr><th>Person</th><th>Kind</th><th>Status</th><th>Messages</th><th>Last</th></tr>
    @foreach($conversations as $conversation)
        <tr>
            <td>
                <a href="{{ route('admin.chat.show', $conversation) }}">{{ $conversation->name ?: 'Visitor '.$conversation->id }}</a>
                @if($conversation->email)<br><small>{{ $conversation->email }}</small>@endif
                @if($conversation->isEmail() && $conversation->subject)<br><small>{{ $conversation->subject }}</small>@endif
            </td>
            <td>
                <span class="kind-pill {{ $conversation->isEmail() ? 'is-email' : '' }}">{{ $conversation->channelLabel() }}</span>
                @if($conversation->awaiting_staff)<div class="chat-waiting">Needs a reply</div>@endif
            </td>
            <td>{{ $conversation->status }}</td>
            <td>{{ $conversation->messages_count }}</td>
            <td>{{ $conversation->last_message_at?->timezone(config('app.timezone'))->format('M j, g:i a') }}</td>
        </tr>
    @endforeach
</table>
{{ $conversations->links() }}
@endif
@endsection
