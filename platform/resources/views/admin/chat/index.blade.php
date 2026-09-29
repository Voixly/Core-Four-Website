@extends('layouts.admin')
@section('title', 'Chat inbox')
@section('content')
<table>
    <tr><th>Visitor</th><th>Audience</th><th>Status</th><th>Messages</th><th>Last</th></tr>
    @foreach($conversations as $conversation)
        <tr>
            <td><a href="{{ route('admin.chat.show', $conversation) }}">{{ $conversation->name ?: 'Visitor '.$conversation->id }}</a>@if($conversation->email)<br><small>{{ $conversation->email }}</small>@endif</td>
            <td>{{ $conversation->audience }}</td>
            <td>{{ $conversation->status }}</td>
            <td>{{ $conversation->messages_count }}</td>
            <td>{{ $conversation->last_message_at }}</td>
        </tr>
    @endforeach
</table>
{{ $conversations->links() }}
@endsection
