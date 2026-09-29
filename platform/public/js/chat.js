(function () {
  const root = document.getElementById('cfr-chat');
  if (!root) return;

  const panel = root.querySelector('.chat-panel');
  const gate = root.querySelector('.chat-gate');
  const gateError = root.querySelector('.chat-gate-error');
  const log = root.querySelector('.chat-log');
  const form = root.querySelector('.chat-compose');
  const input = form.querySelector('input[name="body"]');
  const launcher = root.querySelector('.chat-launcher');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  const audience = root.dataset.audience || 'residential';
  let after = 0;
  let polling = false;

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

  function openChat(data) {
    panel.classList.add('is-ready');
    render(data.messages);
    if (!polling) {
      polling = true;
      poll();
    }
    input.focus();
  }

  async function post(url, body) {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
      body: JSON.stringify(body),
    });
    const data = await res.json().catch(() => ({}));
    return { ok: res.ok, data };
  }

  async function resume() {
    if (panel.classList.contains('is-ready')) return;
    const { ok, data } = await post('/chat/start', { audience, page_url: location.pathname });
    if (ok && data.ready) openChat(data);
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
    if (panel.classList.contains('open')) resume();
  });

  gate.addEventListener('submit', async (e) => {
    e.preventDefault();
    gateError.hidden = true;
    const name = gate.name.value.trim();
    const email = gate.email.value.trim();
    const { ok, data } = await post('/chat/start', { audience, page_url: location.pathname, name, email });
    if (!ok || !data.ready) {
      gateError.hidden = false;
      return;
    }
    openChat(data);
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!panel.classList.contains('is-ready')) return;
    const body = input.value.trim();
    if (!body) return;
    input.value = '';
    const { ok, data } = await post('/chat/send', { body });
    if (!ok) {
      panel.classList.remove('is-ready');
      gateError.hidden = false;
      return;
    }
    render(data.messages);
  });
})();
