@extends('layouts.admin')
@section('title', 'Chat '.$conversation->id)
@section('content')
<div class="panel chat-admin">
    <div class="messages" id="chat-log">
        @foreach($conversation->messages as $message)
            <div class="bubble {{ $message->sender }}">
                <small>{{ $message->sender }}</small><br>{{ $message->body }}
            </div>
        @endforeach
    </div>
    <div class="filters" style="margin-top:1rem">
        @foreach($canned as $reply)
            <button class="btn" type="button" onclick="document.querySelector('[name=body]').value = this.textContent">{{ $reply }}</button>
        @endforeach
    </div>
    <form method="post" action="{{ route('admin.chat.reply', $conversation) }}" style="margin-top:1rem;display:flex;gap:0.5rem">
        @csrf
        <input name="body" required placeholder="Reply as staff">
        <button class="btn" type="submit">Send</button>
    </form>
    <p style="margin-top:1rem">
        <form method="post" action="{{ route('admin.chat.convert', $conversation) }}" style="display:inline">@csrf<button class="btn" type="submit">Convert to lead</button></form>
        <form method="post" action="{{ route('admin.chat.close', $conversation) }}" style="display:inline">@csrf<button class="btn" type="submit">Close</button></form>
    </p>
</div>
<script>
setInterval(async () => {
  const res = await fetch(@json(route('admin.chat.poll', $conversation)));
  const data = await res.json();
  const log = document.getElementById('chat-log');
  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  log.innerHTML = data.messages.map(m => `<div class="bubble ${esc(m.sender)}"><small>${esc(m.sender)}</small><br>${esc(m.body)}</div>`).join('');
}, 4000);
</script>
@endsection
