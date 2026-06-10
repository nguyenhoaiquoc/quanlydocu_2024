// public/js/home.js — Thông báo + Realtime tin nhắn

document.addEventListener('DOMContentLoaded', () => {
  if (!window.appRoutes) {
    console.error('[home] window.appRoutes missing — check script order');
    return;
  }

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // ---------- Helpers ----------
  const setText = (el, txt) => { if (el) el.textContent = txt ?? ''; };
  const hide = (el) => el && el.classList.add('d-none');
  const show = (el) => el && el.classList.remove('d-none');
  const fmtTime = (iso) => new Date(iso || Date.now()).toLocaleString('vi-VN');
  const safe = (s) => String(s ?? '')
    .replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;')
    .replaceAll('"','&quot;').replaceAll("'",'&#039;');

  function showToast(text) {
    const id = 'rt-toast';
    let el = document.getElementById(id);
    if (!el) {
      el = document.createElement('div');
      el.id = id;
      el.className = 'toast align-items-center text-bg-primary position-fixed top-0 end-0 m-3';
      el.setAttribute('role','alert');
      el.innerHTML = `
        <div class="d-flex">
          <div class="toast-body"></div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>`;
      document.body.appendChild(el);
    }
    el.querySelector('.toast-body').textContent = text;
    try { new bootstrap.Toast(el, { delay: 2500 }).show(); } catch(_) {}
  }

  // ---------- Factory ----------
  function createNotificationManager(cfg) {
    // ⬇️ THÊM Ở ĐÂY (Mark All)
  document.getElementById(cfg.markAllBtn)?.addEventListener('click', () => {
    fetch(cfg.routes.markAllRead, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
    }).then(() => {
      (cache.notifications || []).forEach(it => { it.read_at = it.read_at || new Date().toISOString(); });
      cache.unread_count = 0;
      updateBadge();

      const { list } = els();
      list?.querySelectorAll(`.${cfg.itemClass}.unread`).forEach(li => li.classList.remove('unread'));
    }).catch(console.error);
  });
    let cache = { unread_count: 0, notifications: [] };
    let loaded = false;

    const badge = document.getElementById(cfg.badgeId);
    const els = () => ({
      loading: document.getElementById(cfg.loadingId),
      list:    document.getElementById(cfg.listId),
      empty:   document.getElementById(cfg.emptyId),
      error:   document.getElementById(cfg.errorId),
    });

    function updateBadge() {
      const unread = cache?.unread_count ?? 0;
      if (badge) {
        setText(badge, unread > 0 ? unread : '');
        badge.style.display = unread > 0 ? 'inline' : 'none';
      }
    }

    function renderList(items) {
      const { list } = els();
      if (!list) return;
      list.innerHTML = items.map(cfg.template).join('');
      show(list);
      attachItemEvents();
    }

    function attachItemEvents() {
      const { list } = els();
      list?.querySelectorAll(`.${cfg.itemClass}.unread`)?.forEach(li => {
        li.addEventListener('click', () => markRead(li));
      });
    }

    function markRead(li) {
      const id = li.getAttribute('data-key');
      fetch(cfg.routes.markRead.replace(':id', id), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
      }).then(() => {
        li.classList.remove('unread');
        cache.unread_count = Math.max((cache.unread_count ?? 1) - 1, 0);
        updateBadge();
      }).catch(console.error);
    }

    return {
      // fetchData(renderUI = true, merge = false)
      fetchData(render = true, merge = false) {
        const url = cfg.routes.list;
        if (!url) return;

        const d = els();
        if (render) { show(d.loading); hide(d.list); hide(d.empty); hide(d.error); }

        fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
          .then(r => { if (!r.ok) throw r; return r.json(); })
        .then(json => {
  // Chấp nhận cả 2 shape:
  // - cũ: { unread_count, notifications: [{ id, read_at, created_at, data:{...} }] }
  // - mới: { unread, data: [{ key, is_read, datetime, time_ago, ...flat fields... }] }

  const incoming = json || {};

  const unread = (typeof incoming.unread === 'number')
    ? incoming.unread
    : (typeof incoming.unread_count === 'number' ? incoming.unread_count : 0);

  // items chuẩn hoá về dạng "cũ" mà file này đang dùng: [{ id, read_at, created_at, data:{...} }]
  let items = [];
  if (Array.isArray(incoming.notifications)) {
    // shape cũ -> giữ nguyên
    items = incoming.notifications;
  } else if (Array.isArray(incoming.data)) {
    // shape mới -> map sang shape cũ
    items = incoming.data.map(it => ({
      id: it.key ?? it.id ?? String(Math.random()),
      read_at: it.is_read ? (it.read_at || it.datetime || new Date().toISOString()) : null,
      created_at: it.datetime || it.created_at || new Date().toISOString(),
      data: {
        // Follow
        profile_url: it.profile_url,
        avatar: it.avatar,
        name: it.name,
        direction: it.direction,
        // Comment
        type: it.type,
        user_name: it.user_name,
        snippet: it.snippet,
        // Product
        product_id: it.product_id,
        product_url: it.product_url,
        message: it.message,
        // Message
        actor_name: it.actor_name,
        user_name2: it.user_name, // tránh đè
        sender_name: it.sender_name,
        chat_url: it.chat_url,
      }
    }));
  }

  // cập nhật cache theo cấu trúc cũ mà template đang dùng
  cache = { unread_count: unread, notifications: items };

  loaded = true;
  updateBadge();

  if (!render) return;

  const nd = els();
  hide(nd.loading); hide(nd.error);
  if (!items.length) { hide(nd.list); show(nd.empty); }
  else { hide(nd.empty); renderList(items); }
})

          .catch(async err => {
            console.error(`[home] Fetch ${cfg.badgeId} error:`, err);
            try { const body = await err.text?.(); if (body) console.error('[home] body:', body); } catch(_) {}
            if (render) { const nd = els(); hide(nd.loading); show(nd.error); }
          });
      },

      append(item) {
        // 1) update cache + badge
        cache.notifications ||= [];
        cache.notifications.unshift(item);
        cache.unread_count = (cache.unread_count || 0) + 1;
        updateBadge();

        // 2) DOM mỗi lần
        const { loading, list, empty } = els();

        // Nếu panel đang mở và không ở tab "message", ép chuyển tab
        const panel = document.getElementById('notificationPanel');
        if (panel?.classList.contains('show') && cfg.listId === 'message-list') {
          document.querySelectorAll('#notifTabs .nav-link').forEach(t => t.classList.remove('active'));
          document.querySelector('#notifTabs .nav-link[data-tab="message"]')?.classList.add('active');
          document.querySelectorAll('.tab-content').forEach(c => c.classList.add('d-none'));
          document.getElementById('tab-message')?.classList.remove('d-none');
        }

        // 3) ép hiển thị list
        loading && loading.classList.add('d-none');
        empty && empty.classList.add('d-none');

        if (list) {
          list.classList.remove('d-none');
          list.insertAdjacentHTML('afterbegin', cfg.template(item));
          const first = list.querySelector(`.${cfg.itemClass}.unread`);
          first && first.addEventListener('click', () => markRead(first));
        } else {
          console.warn('[home][append] #' + cfg.listId + ' not found');
        }
      },

      isLoaded: () => loaded,

      renderCached() {
        const d = els();
        hide(d.loading); hide(d.error);
        const items = cache.notifications || [];
        if (!items.length) { hide(d.list); show(d.empty); return; }
        hide(d.empty); renderList(items);
      },
    };
  }

  // ---------- Managers ----------
 const productManager = createNotificationManager({
  badgeId: 'product-badge', loadingId: 'product-loading', listId: 'product-list',
  emptyId: 'product-empty', errorId: 'product-error', itemClass: 'product-item',
  markAllBtn: 'markAllProductRead',
  routes: {
    list: window.appRoutes.product.list,
    markRead: window.appRoutes.product.read,
    markAllRead: window.appRoutes.product.readAll
  },
  template: (item) => {
    const time = fmtTime(item.created_at, item.data.time_ago);
    // ưu tiên message_html, fallback sang message (escaped)
    const msg = item.data.message_html ?? safe(item.data.message ?? '');
    const href = item.data.product_url || (item.data.product_id ? `/products/${item.data.product_id}` : '#');
    return `
      <li data-key="${item.id}" class="product-item ${item.read_at ? '' : 'unread'}">
        <a href="${href}">
          <span class="notif-message">${msg}</span>
          <small>${time}</small>
        </a>
      </li>`;
  }
});


  const followManager = createNotificationManager({
    badgeId: 'follow-badge', loadingId: 'follow-loading', listId: 'follow-list',
    emptyId: 'follow-empty', errorId: 'follow-error', itemClass: 'follow-item',
    markAllBtn: 'markAllFollowRead',
    routes: {
      list: window.appRoutes.follow.list,
      markRead: window.appRoutes.follow.read,
      markAllRead: window.appRoutes.follow.readAll
    },
    template: (item) => `
      <li data-key="${item.id}" class="follow-item ${item.read_at ? '' : 'unread'}">
        <a href="${item.data.profile_url}">
          <img src="${item.data.avatar}" alt="${safe(item.data.name)}" onerror="this.style.display='none'">
          <div class="notif-text">
            ${item.data.direction === 'in'
              ? `<strong>${safe(item.data.name)}</strong> đã theo dõi bạn`
              : `Bạn đã theo dõi <strong>${safe(item.data.name)}</strong>`}
            <small>${fmtTime(item.created_at)}</small>
          </div>
        </a>
      </li>`
  });

  const commentManager = createNotificationManager({
    badgeId: 'comment-badge', loadingId: 'comment-loading', listId: 'comment-list',
    emptyId: 'comment-empty', errorId: 'comment-error', itemClass: 'comment-item',
    markAllBtn: 'markAllCommentRead',
    routes: {
      list: window.appRoutes.comment.list,
      markRead: window.appRoutes.comment.read,
      markAllRead: window.appRoutes.comment.readAll
    },
    template: (item) => `
      <li data-key="${item.id}" class="comment-item ${item.read_at ? '' : 'unread'}">
        <a href="${item.data.profile_url}">
          <img src="${item.data.avatar}" alt="${safe(item.data.user_name)}" onerror="this.style.display='none'">
          <div class="notif-text">
            ${
              item.data.type === 'reply'
                ? `<strong>${safe(item.data.user_name)}</strong> đã trả lời bình luận của bạn${item.data.snippet ? `: <em>${safe(item.data.snippet)}</em>` : ''}`
                : `<strong>${safe(item.data.user_name)}</strong> đã bình luận trên trang cá nhân của bạn${item.data.snippet ? `: <em>${safe(item.data.snippet)}</em>` : ''}`
            }
            <small>${fmtTime(item.created_at)}</small>
          </div>
        </a>
      </li>`
  });

  const messageManager = createNotificationManager({
    badgeId: 'message-badge', loadingId: 'message-loading', listId: 'message-list',
    emptyId: 'message-empty', errorId: 'message-error', itemClass: 'message-item',
    markAllBtn: 'markAllMessageRead',
    routes: {
      list: window.appRoutes.message.list,
      markRead: window.appRoutes.message.read,
      markAllRead: window.appRoutes.message.readAll
    },
   template: (item) => `
    <li data-key="${item.id}" class="message-item ${item.read_at ? '' : 'unread'}">
      <a href="${item.data.chat_url}">
        <img src="${item.data.avatar}"
             alt="${safe(item.data.actor_name || item.data.user_name || item.data.sender_name || 'Người dùng')}"
             onerror="this.src='${window.DEFAULT_AVATAR || '/images/default-avatar.png'}'">
        <div class="notif-text">
          <strong>${safe(item.data.actor_name || item.data.user_name || item.data.sender_name || 'Người dùng')}</strong>
          ${item.data.snippet ? `: <em>${safe(item.data.snippet)}</em>` : `: ${safe(item.data.message || 'Tin nhắn mới')}`}
          <small>${fmtTime(item.created_at)}</small>
        </div>
      </a>
    </li>`
  });

  // để debug nếu cần
  window.messageManager = messageManager;
  window.followManager  = followManager;   
window.productManager = productManager;  
window.commentManager = commentManager; 

  // ---------- Fetch badge khi load (không render list) ----------
messageManager.fetchData(false, true);
followManager.fetchData(false, true);
productManager.fetchData(false, true);
commentManager.fetchData(false, true);


  // --- Đặt ngay cuối file home.js, sau khi messageManager.fetchData(false) ---
let _lastUnread = 0;

// đọc lần đầu để có mốc
setTimeout(() => {
  const badge = document.getElementById('message-badge');
  _lastUnread = parseInt((badge?.textContent || '0').trim(), 10) || 0;
}, 800);

// Poll mỗi 20s
setInterval(async () => {
  try {
    await messageManager.fetchData(false, true); // merge

    const badge = document.getElementById('message-badge');
    const cur = parseInt((badge?.textContent || '0').trim(), 10) || 0;

    const panelOpen = document.getElementById('notificationPanel')?.classList.contains('show');

    if (!panelOpen && cur > _lastUnread) {
      const diff = cur - _lastUnread;
      showToast(diff === 1 ? 'Bạn có 1 tin nhắn mới' : `Bạn có ${diff} tin nhắn mới`);
    }
    _lastUnread = cur;
  } catch(_) {}
}, 20000);

  // ---------- Mở / Đóng panel ----------
  document.getElementById('notifBell')?.addEventListener('click', () => {
    const panel = document.getElementById('notificationPanel');
    panel?.classList.add('show');

    // render cache có sẵn (gồm item realtime vừa append)
    messageManager.renderCached();
    followManager.renderCached();
    productManager.renderCached();
    commentManager.renderCached();

    // merge với server ở nền, không làm mất item realtime
    messageManager.fetchData(true, true);
    followManager.fetchData(true, true);
    productManager.fetchData(true, true);
    commentManager.fetchData(true, true);

    // nếu list đã có item, đảm bảo không bị d-none
    const ml = document.getElementById('message-list');
    const me = document.getElementById('message-empty');
    if (ml && ml.children.length > 0) { ml.classList.remove('d-none'); me?.classList.add('d-none'); }
  });

  document.getElementById('closeNotif')?.addEventListener('click', () => {
    document.getElementById('notificationPanel')?.classList.remove('show');
  });

  // ---------- Tabs ----------
  document.querySelectorAll('#notifTabs .nav-link').forEach(tab => {
    tab.addEventListener('click', function () {
      document.querySelectorAll('#notifTabs .nav-link').forEach(t => t.classList.remove('active'));
      this.classList.add('active');

      document.querySelectorAll('.tab-content').forEach(c => c.classList.add('d-none'));
      const pane = document.getElementById('tab-' + this.dataset.tab);
      pane && pane.classList.remove('d-none');

      switch (this.dataset.tab) {
        case 'message': messageManager.renderCached(); break;
        case 'follow':  followManager.renderCached();  break;
        case 'product': productManager.renderCached(); break;
        case 'comment': commentManager.renderCached(); break;
      }
    });
  });

  // ---------- Realtime: user.{id} ----------
  if (window.echo && window.APP_USER_ID) {
    const ch = window.echo.private(`user.${window.APP_USER_ID}`);

    try {
      const raw = ch._pusherChannel || ch.subscription;
      raw?.bind?.('pusher:subscription_succeeded', () =>
        console.log('[home] subscribed user.' + window.APP_USER_ID));
      raw?.bind?.('pusher:subscription_error', e =>
        console.error('[home] subscription_error', e));
    } catch(_) {}

    ch.listen('.message.created', (e) => {
      console.log('[home] message.created received:', e);

      const avatar = e.sender_avatar || window.DEFAULT_AVATAR || '/images/default-avatar.png';
      const name   = e.sender_name || 'Người dùng';

      const item = {
        id: `evt-${e.id}`,
        read_at: null,
        created_at: new Date().toISOString(),
        data: {
          actor_name: name,
          avatar: avatar,
          message: 'Tin nhắn mới',
          snippet: e.message || '',
          chat_url: `/chat/${e.chat_id}`
        }
      };

      messageManager.append(item);

      const panelOpen = document.getElementById('notificationPanel')?.classList.contains('show');
      if (!panelOpen) showToast(`${name}: ${e.message || 'đã gửi tin nhắn'}`);
    });
  } else {
    console.warn('[home] thiếu echo hoặc APP_USER_ID');
  }

  // ---------- Tự kết nối lại & đồng bộ badge ----------
document.addEventListener('visibilitychange', () => {
  if (!document.hidden) {
    try { window.echo?.connect(); } catch(_) {}
    messageManager.fetchData(false, true);
  }
});

window.addEventListener('online', () => {
  try { window.echo?.connect(); } catch(_) {}
  messageManager.fetchData(false, true);
});

window.addEventListener('focus', () => {
  try { window.echo?.connect(); } catch(_) {}
  messageManager.fetchData(false, true);
});

});


