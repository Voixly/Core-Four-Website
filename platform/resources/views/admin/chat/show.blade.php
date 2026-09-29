@extends('layouts.admin')
@section('title', $conversation->channelLabel().' · '.($conversation->name ?: 'Visitor'))
@section('content')
<div class="panel chat-admin">
    <div class="chat-kind">
        <span class="kind-pill {{ $conversation->isEmail() ? 'is-email' : '' }}">{{ $conversation->channelLabel() }}</span>
        @if($conversation->awaiting_staff)<span class="chat-waiting">Needs a reply</span>@endif
    </div>
    <p>{{ $conversation->name ?: 'No name' }}@if($conversation->email) · {{ $conversation->email }}@endif</p>
    @if($conversation->isEmail())
        <p class="chat-note">Subject: {{ $conversation->subject ?: 'Core Four Roofing' }}. A reply here is emailed to {{ $conversation->email }}. It does not show up in the website chat.</p>
    @else
        <p class="chat-note">This is the website chat. A reply stays on the site and is not emailed.</p>
    @endif
    @if($conversation->lead)
        <p><a class="btn" href="{{ route('admin.leads.show', $conversation->lead) }}">Open lead</a></p>
    @endif
    <div class="messages" id="chat-log">
        @foreach($conversation->messages as $message)
            <div class="bubble {{ $message->sender }}">
                <small>{{ $message->sender === 'staff' ? 'Office' : ($conversation->isEmail() && $message->sender === 'visitor' ? 'Email' : $message->sender) }}</small><br>{{ $message->body }}
            </div>
        @endforeach
    </div>
    <div class="filters" style="margin-top:1rem">
        @foreach($canned as $reply)
            <button class="btn" type="button" onclick="document.querySelector('[name=body]').value = this.textContent">{{ $reply }}</button>
        @endforeach
    </div>
    <form method="post" action="{{ route('admin.chat.reply', $conversation) }}" class="chat-compose">
        @csrf
        <textarea name="body" required rows="5" placeholder="{{ $conversation->isEmail() ? 'Write the email' : 'Reply in the website chat' }}">{{ old('body') }}</textarea>
        <button class="btn" type="submit">{{ $conversation->isEmail() ? 'Send email' : 'Send' }}</button>
    </form>
    <p style="margin-top:1rem">
        @unless($conversation->lead_id)
            <form method="post" action="{{ route('admin.chat.convert', $conversation) }}" style="display:inline">@csrf<button class="btn" type="submit">Convert to lead</button></form>
        @endunless
        <form method="post" action="{{ route('admin.chat.close', $conversation) }}" style="display:inline">@csrf<button class="btn" type="submit">Close</button></form>
    </p>
</div>
<script>
setInterval(async () => {
  const res = await fetch(@json(route('admin.chat.poll', $conversation)));
  const data = await res.json();
  const log = document.getElementById('chat-log');
  const emailThread = @json($conversation->isEmail());
  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const label = (sender) => sender === 'staff' ? 'Office' : (emailThread && sender === 'visitor' ? 'Email' : sender);
  log.innerHTML = data.messages.map(m => `<div class="bubble ${esc(m.sender)}"><small>${esc(label(m.sender))}</small><br>${esc(m.body)}</div>`).join('');
}, 4000);
</script>
@endsection
