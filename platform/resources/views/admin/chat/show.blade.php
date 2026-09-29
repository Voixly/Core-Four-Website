@extends('layouts.admin')
@section('title', $conversation->channelLabel().' · '.($conversation->name ?: 'Visitor'))
@section('content')
@php
    $visitorLabel = $conversation->isEmail() ? 'Email' : ($conversation->name ?: 'Visitor');
    $bubbleLabel = function (string $sender) use ($visitorLabel): string {
        return match ($sender) {
            'staff' => 'Office',
            'bot' => 'Core Four',
            default => $visitorLabel,
        };
    };
@endphp
<div class="chat-thread">
    <div class="panel chat-head">
        <div>
            <div class="chat-kind">
                <span class="kind-pill {{ $conversation->isEmail() ? 'is-email' : '' }}">{{ $conversation->channelLabel() }}</span>
                <span class="tag tag-{{ $conversation->status }}">{{ $conversation->status }}</span>
                @if($conversation->awaiting_staff)<span class="chat-waiting">Needs a reply</span>@endif
            </div>
            <h2>{{ $conversation->name ?: 'No name' }}</h2>
            <p class="chat-note">
                @if($conversation->email){{ $conversation->email }}@endif
                @if($conversation->phone) · {{ $conversation->phone }}@endif
                @if($conversation->audience && $conversation->audience !== 'unknown') · {{ ucfirst($conversation->audience) }}@endif
            </p>
            @if($conversation->isEmail())
                <p class="chat-note">Subject: {{ $conversation->subject ?: 'Core Four Roofing' }}. Sending from here emails {{ $conversation->email }}.</p>
            @else
                <p class="chat-note">Website chat. A reply stays on the site.@if($conversation->page_url) They were on <a href="{{ $conversation->page_url }}">{{ $conversation->page_url }}</a>.@endif</p>
            @endif
        </div>
        <div class="chat-actions">
            @if($conversation->lead)
                <a class="btn btn-ghost" href="{{ route('admin.leads.show', $conversation->lead) }}">Open lead</a>
            @else
                <form method="post" action="{{ route('admin.chat.convert', $conversation) }}">@csrf<button class="btn" type="submit">Convert to lead</button></form>
            @endif
            <form method="post" action="{{ route('admin.chat.close', $conversation) }}">@csrf<button class="btn btn-ghost" type="submit">Close</button></form>
        </div>
    </div>

    <div class="chat-log" id="chat-log">
        @foreach($conversation->messages as $message)
            <div class="bubble {{ $message->sender }}">
                <div class="bubble-meta">
                    <span>{{ $bubbleLabel($message->sender) }}</span>
                    <time>{{ $message->created_at?->timezone(config('app.timezone'))->format('M j, g:i a') }}</time>
                </div>
                <div class="bubble-body">{{ $message->body }}</div>
            </div>
        @endforeach
    </div>

    <form method="post" action="{{ route('admin.chat.reply', $conversation) }}" class="panel chat-compose">
        @csrf
        <div class="chat-canned">
            @foreach($canned as [$label, $reply])
                <button class="chip" type="button" data-reply="{{ $reply }}">{{ $label }}</button>
            @endforeach
        </div>
        <div class="chat-send">
            <textarea name="body" required rows="3" placeholder="{{ $conversation->isEmail() ? 'Write the email' : 'Reply in the website chat' }}">{{ old('body') }}</textarea>
            <button class="btn" type="submit">{{ $conversation->isEmail() ? 'Send email' : 'Send' }}</button>
        </div>
    </form>
</div>
<script>
(() => {
  const log = document.getElementById('chat-log');
  const emailThread = @json($conversation->isEmail());
  const visitorLabel = @json($visitorLabel);
  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const label = (sender) => sender === 'staff' ? 'Office' : (sender === 'bot' ? 'Core Four' : visitorLabel);
  const when = (iso) => {
    if (!iso) return '';
    return new Date(iso).toLocaleString('en-US', { timeZone: 'America/Chicago', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
  };
  const paint = (messages) => {
    const nearBottom = log.scrollHeight - log.scrollTop - log.clientHeight < 90;
    log.innerHTML = messages.map((m) => `<div class="bubble ${esc(m.sender)}"><div class="bubble-meta"><span>${esc(label(m.sender))}</span><time>${esc(when(m.created_at))}</time></div><div class="bubble-body">${esc(m.body)}</div></div>`).join('');
    if (nearBottom) log.scrollTop = log.scrollHeight;
  };
  document.querySelectorAll('.chat-canned .chip').forEach((button) => {
    button.addEventListener('click', () => {
      const field = document.querySelector('[name=body]');
      field.value = button.dataset.reply;
      field.focus();
    });
  });
  log.scrollTop = log.scrollHeight;
  setInterval(async () => {
    const res = await fetch(@json(route('admin.chat.poll', $conversation)));
    const data = await res.json();
    paint(data.messages);
  }, 4000);
})();
</script>
@endsection
