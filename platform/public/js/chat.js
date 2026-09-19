(function () {
  const root = document.getElementById('cfr-chat');
  if (!root) return;

  const panel = root.querySelector('.chat-panel');
  const log = root.querySelector('.chat-log');
  const form = root.querySelector('.chat-compose');
  const input = form.querySelector('input[name="body"]');
  const launcher = root.querySelector('.chat-launcher');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  const audience = root.dataset.audience || 'residential';
  let after = 0;
  let started = false;

  function bubble(msg) {
    const el = document.createElement('div');
    el.className = 'bubble ' + (msg.sender || 'bot');
    el.textContent = msg.body;
    el.dataset.id = msg.id;
    log.appendChild(el);
    log.scrollTop = log.scrollHeight;
    after = Math.max(after, Number(msg.id) || 0);
  }

  function render(messages) {
    (messages || []).forEach((msg) => {
      if (!log.querySelector('[data-id="' + msg.id + '"]')) bubble(msg);
    });
  }

  async function start() {
    if (started) return;
    started = true;
    const res = await fetch('/chat/start', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
      body: JSON.stringify({ audience, page_url: location.pathname }),
    });
    const data = await res.json();
    render(data.messages);
    poll();
  }

  async function poll() {
    try {
      const res = await fetch('/chat/poll?after=' + after, { headers: { Accept: 'application/json' } });
      if (res.ok) {
        const data = await res.json();
        render(data.messages);
      }
    } catch (e) {}
    setTimeout(poll, 3000);
  }

  launcher.addEventListener('click', () => {
    panel.classList.toggle('open');
    if (panel.classList.contains('open')) start();
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const body = input.value.trim();
    if (!body) return;
    input.value = '';
    await start();
    const res = await fetch('/chat/send', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
      body: JSON.stringify({ body }),
    });
    const data = await res.json();
    render(data.messages);
  });
})();
