@extends('layouts.admin')
@section('title', 'Chat inbox')
@section('meta', $counts['waiting'] ? $counts['waiting'].' email '.($counts['waiting'] === 1 ? 'reply needs' : 'replies need').' an answer' : 'Website chats and email replies')
@section('content')
<div class="filters">
    <a class="kind-pill {{ $channel ? '' : 'is-on' }}" href="{{ route('admin.chat.index') }}">All {{ $counts['all'] }}</a>
    <a class="kind-pill {{ $channel === 'chat' ? 'is-on' : '' }}" href="{{ route('admin.chat.index', ['channel' => 'chat']) }}">Live chat {{ $counts['chat'] }}</a>
    <a class="kind-pill is-email {{ $channel === 'email' ? 'is-on' : '' }}" href="{{ route('admin.chat.index', ['channel' => 'email']) }}">Email {{ $counts['email'] }}</a>
</div>
@unless(config('services.resend.webhook_secret'))
    <p class="chat-note">Email replies show up here after the reply domain is connected. Until then, a reply still arrives at hello@corefourroofing.com.</p>
@endunless
@if($conversations->isEmpty())
    <div class="panel"><p>No conversations in this inbox yet.</p></div>
@else
    <div class="chat-inbox">
        @foreach($conversations as $conversation)
            @php
                $preview = trim(preg_replace('/\s+/', ' ', (string) $conversation->latestMessage?->body) ?? '');
            @endphp
            <a class="chat-row {{ $conversation->awaiting_staff ? 'is-waiting' : '' }}" href="{{ route('admin.chat.show', $conversation) }}">
                <div class="chat-row-main">
                    <div class="chat-row-title">
                        <strong>{{ $conversation->name ?: 'Visitor '.$conversation->id }}</strong>
                        <span class="kind-pill {{ $conversation->isEmail() ? 'is-email' : '' }}">{{ $conversation->channelLabel() }}</span>
                        @if($conversation->awaiting_staff)<span class="chat-waiting">Needs a reply</span>@endif
                    </div>
                    @if($conversation->email)<div class="chat-row-email">{{ $conversation->email }}</div>@endif
                    @if($preview !== '')<p class="chat-preview">{{ \Illuminate\Support\Str::limit($preview, 110) }}</p>@endif
                </div>
                <div class="chat-row-side">
                    <span class="tag tag-{{ $conversation->status }}">{{ $conversation->status }}</span>
                    <time>{{ $conversation->last_message_at?->timezone(config('app.timezone'))->format('M j, g:i a') }}</time>
                    <span>{{ $conversation->messages_count }} {{ $conversation->messages_count === 1 ? 'message' : 'messages' }}</span>
                </div>
            </a>
        @endforeach
    </div>
    {{ $conversations->links() }}
@endif
@endsection
