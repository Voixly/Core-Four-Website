(function () {
    const board = document.querySelector('[data-kanban]');
    if (!board) {
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const cols = () => [...board.querySelectorAll('.kanban-col')];
    let drag = null;

    function recount() {
        cols().forEach((col) => {
            const badge = col.querySelector('[data-count]');
            if (badge) {
                badge.textContent = String(col.querySelectorAll('.kanban-card').length);
            }
        });
    }

    function columnAt(x, y) {
        return cols().find((col) => {
            const box = col.getBoundingClientRect();
            return x >= box.left && x <= box.right && y >= box.top && y <= box.bottom;
        }) || null;
    }

    function clearOver() {
        cols().forEach((col) => col.classList.remove('is-over'));
    }

    function notice(message, isError) {
        let bar = document.querySelector('[data-kanban-flash]');
        if (!bar) {
            bar = document.createElement('div');
            bar.className = 'flash';
            bar.setAttribute('data-kanban-flash', '');
            board.parentNode.insertBefore(bar, board);
        }
        bar.textContent = message;
        bar.classList.toggle('is-error', Boolean(isError));
        bar.hidden = false;
    }

    function finish(ok, message) {
        if (!drag) {
            return;
        }
        if (!ok && drag.from) {
            drag.from.querySelector('.kanban-list').appendChild(drag.card);
            recount();
        }
        drag.card.classList.remove('is-dragging');
        drag.ghost.remove();
        document.body.classList.remove('is-kanban-drag');
        clearOver();
        if (message) {
            notice(message, !ok);
        }
        drag = null;
    }

    async function dropOn(col) {
        if (!drag || !col) {
            finish(true);
            return;
        }

        const stageId = col.getAttribute('data-stage-id');
        const select = drag.card.querySelector('select[name="stage_id"]');
        const same = drag.from === col;

        if (!same) {
            col.querySelector('.kanban-list').appendChild(drag.card);
            recount();
        }

        if (same) {
            finish(true);
            return;
        }

        try {
            const res = await fetch(drag.card.getAttribute('data-move'), {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ stage_id: Number(stageId) }),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || data.ok === false) {
                finish(false, data.message || 'Could not move that job.');
                return;
            }
            if (select) {
                select.value = stageId;
            }
            finish(true, 'Moved to ' + (data.stage || 'the next stage') + '.');
        } catch (e) {
            finish(false, 'Could not move that job.');
        }
    }

    board.addEventListener('pointerdown', (event) => {
        if (event.button && event.button !== 0) {
            return;
        }
        if (event.target.closest('a, select, button, input, textarea, label')) {
            return;
        }
        const card = event.target.closest('.kanban-card');
        if (!card || !board.contains(card)) {
            return;
        }

        const startX = event.clientX;
        const startY = event.clientY;
        const from = card.closest('.kanban-col');
        const box = card.getBoundingClientRect();
        const offsetX = startX - box.left;
        const offsetY = startY - box.top;
        let started = false;

        const onMove = (moveEvent) => {
            const dx = moveEvent.clientX - startX;
            const dy = moveEvent.clientY - startY;
            if (!started && Math.hypot(dx, dy) < 6) {
                return;
            }
            if (!started) {
                started = true;
                const ghost = card.cloneNode(true);
                ghost.classList.add('kanban-ghost');
                ghost.style.width = box.width + 'px';
                document.body.appendChild(ghost);
                card.classList.add('is-dragging');
                document.body.classList.add('is-kanban-drag');
                drag = { card, from, ghost };
            }
            drag.ghost.style.left = (moveEvent.clientX - offsetX) + 'px';
            drag.ghost.style.top = (moveEvent.clientY - offsetY) + 'px';
            clearOver();
            columnAt(moveEvent.clientX, moveEvent.clientY)?.classList.add('is-over');
            moveEvent.preventDefault();
        };

        const onUp = (upEvent) => {
            window.removeEventListener('pointermove', onMove);
            window.removeEventListener('pointerup', onUp);
            window.removeEventListener('pointercancel', onUp);
            if (!started) {
                return;
            }
            dropOn(columnAt(upEvent.clientX, upEvent.clientY) || drag.from);
        };

        window.addEventListener('pointermove', onMove, { passive: false });
        window.addEventListener('pointerup', onUp);
        window.addEventListener('pointercancel', onUp);
    });
})();
