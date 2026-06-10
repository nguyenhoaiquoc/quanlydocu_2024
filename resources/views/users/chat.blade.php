<x-layout>
   <x-chat-layout></x-chat-layout>
<script>
const initialChatId = {{ $initialChatId ?? 'null' }};
let currentUserId = null;
let currentChatId = null;
let pollingInterval;
let conversations = [];
const token = localStorage.getItem('sanctum_token');
let forceScrollBottomOnce = false;
let docClickBound = false;

// ===== NEW: state ảnh chọn trước khi gửi =====
let selectedFiles = []; // Array<File> (tối đa 4)


function getHeaders(isJson = true) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const h = { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf };
  if (isJson) h['Content-Type'] = 'application/json';
  const t = localStorage.getItem('sanctum_token');
  if (t) h['Authorization'] = `Bearer ${t}`; // chỉ set khi có token
  return h;
}


// ========== USER ==========
async function getCurrentUser() {
  try {
const res = await fetch('/api/user', { method: 'GET', headers: getHeaders(), credentials: 'include' });
    if (!res.ok) throw new Error('Failed to fetch user');
    const user = await res.json();
    currentUserId = user.id;
  } catch (err) {
    console.error('Lỗi khi lấy user:', err);
  }
}

// ========== CONVERSATIONS ==========
async function loadConversations(query = '') {
  try {
    const url = new URL('/api/chats', window.location.origin);
    if (query) url.searchParams.set('q', query);

    const res = await fetch(url, { method: 'GET', headers: getHeaders(), credentials: 'include' });
    if (!res.ok) throw new Error('Failed to load conversations');

    conversations = await res.json();

    const cur = conversations.find(c => c.id === currentChatId);
    if (cur?.status) {
      const st = document.querySelector('.chat-header .status');
      if (st) st.textContent = cur.status;
    }

    const list = document.querySelector('.conversation-list');
    list.innerHTML = '';
    conversations.forEach(c => {
      const div = document.createElement('div');
      div.classList.add('conversation-item');
      if (c.id === currentChatId) div.classList.add('active');
      div.innerHTML = `
        <img src="${c.image || 'https://via.placeholder.com/48?text=U'}" alt="avatar">
        <div class="info">
          <span class="name">${c.name}</span>
          <span class="preview">${c.lastMessage}</span>
        </div>
        <span class="timestamp">${c.timestamp}</span>
      `;
      div.addEventListener('click', () => openChat(c.id));
      list.appendChild(div);
    });
  } catch (err) {
    console.error('Lỗi load conversations:', err);
  }
}


(function bindSearch() {
  const input = document.getElementById('search-input');
  if (!input) return;

  let t = null;
  input.addEventListener('input', () => {
    const q = input.value.trim();
    clearTimeout(t);
    t = setTimeout(() => {
      if (q.length >= 2) {
        loadConversations(q);     // gọi API có q
      } else if (q.length === 0) {
        loadConversations();      // trả về full list
      }
      // nếu 1-2 ký tự: không gọi (theo placeholder bạn ghi)
    }, 250);
  });
})();


// ========== OPEN CHAT ==========
function openChat(chatId) {
  const isSwitching = currentChatId !== chatId;   // <— thêm
  currentChatId = chatId;

  const chat = conversations.find(c => c.id === chatId);
  if (chat) {
    document.getElementById('chat-avatar').src = chat.image || 'https://via.placeholder.com/48?text=U';
    document.getElementById('chat-name').textContent = chat.name;

      const linkEl = document.getElementById('chat-user-link');
    if (linkEl && chat.profile_url) linkEl.href = chat.profile_url;

    if (chat.product) {
      document.querySelector('.product-card img').src = chat.product.image || 'https://via.placeholder.com/80';
      document.querySelector('.product-card .title').textContent = chat.product.name || 'Sản phẩm mặc định';
      document.querySelector('.product-card .price').textContent = chat.product.price || 'Giá mặc định';
    }
    if (chat && chat.status) {
  const st = document.querySelector('.chat-header .status');
  if (st) st.textContent = chat.status;
}

  }

  // Bật cờ để loadMessages cuộn xuống
  if (isSwitching) forceScrollBottomOnce = true;   // <— thêm

  loadMessages();
  clearInterval(pollingInterval);
  pollingInterval = setInterval(loadMessages, 5000);

  document.querySelector('.no-chat').style.display = 'none';
  const chatView = document.querySelector('.chat-view');
  chatView.style.display = 'flex';
  if (getComputedStyle(chatView).position === 'static') chatView.style.position = 'relative';
  document.querySelector('.chat-container').classList.add('chat-open');

   // cập nhật URL /chat/{id} cho đẹp (không reload)
  if (window.history?.replaceState) {
    history.replaceState(null, '', `/chat/${chatId}`);
  }
  loadConversations();
}


// ========== LOAD MESSAGES ==========
function loadMessages() {
  if (!currentChatId) return;
fetch(`/api/chats/${currentChatId}/messages`, { method: 'GET', headers: getHeaders(), credentials: 'include' })
    .then(res => {
      if (!res.ok) throw new Error('Failed to load messages');
      return res.json();
    })
    .then(messages => {
      const container = document.querySelector('.message-area');
      const scrollBtn = document.getElementById('scroll-down-btn');
      container.innerHTML = '';

      messages.forEach(msg => {
        const div = document.createElement('div');
        const isOutgoing = msg.sender_id === currentUserId;
        const isRevoked  = !!msg.is_revoked;
        div.classList.add('bubble', isOutgoing ? 'outgoing' : 'incoming');

        const time = new Date(msg.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        const safeText = msg.display_message ?? '';

        let contentHtml = '';
        if (msg.file_url) {
          if (msg.file_type && msg.file_type.startsWith('image')) {
            contentHtml = `<img src="${msg.file_url}" style="max-width:200px;border-radius:8px;">`;
          } else if (msg.file_type && msg.file_type.startsWith('video')) {
            contentHtml = `<video controls style="max-width:200px;border-radius:8px;">
                              <source src="${msg.file_url}" type="${msg.file_type}">
                           </video>`;
          } else {
            contentHtml = `<a href="${msg.file_url}" target="_blank">Tải tệp</a>`;
          }
        } else {
          contentHtml = `<p class="${isRevoked ? 'revoked' : ''}">${safeText}</p>`;
        }

        div.innerHTML = `
          ${contentHtml}
          <span class="time">${time}</span>
          <div class="more-container" style="display:${(isOutgoing && !isRevoked) ? 'block' : 'none'}">
            <button class="more-btn">⋯</button>
            <div class="context-menu">
              ${isOutgoing && !isRevoked ? '<button class="recall-btn">Thu hồi</button>' : ''}
              ${!isRevoked ? '<button class="copy-btn">Sao chép</button>' : ''}
            </div>
          </div>
        `;

        const moreContainer = div.querySelector('.more-container');
        const moreBtn = div.querySelector('.more-btn');
        const menu = div.querySelector('.context-menu');
        let hideTimeout;

        div.addEventListener('mouseenter', () => {
          clearTimeout(hideTimeout);
          if (menu && menu.style.display !== 'flex') moreBtn.style.display = 'inline-flex';
        });
        div.addEventListener('mouseleave', () => {
          hideTimeout = setTimeout(() => {
            if (menu && menu.style.display !== 'flex') moreBtn.style.display = 'none';
          }, 200);
        });

        if (moreBtn) {
          moreBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            document.querySelectorAll('.more-container.active').forEach(c => {
              if (c !== moreContainer) {
                c.classList.remove('active');
                const m = c.querySelector('.context-menu');
                const btn = c.querySelector('.more-btn');
                if (m) m.style.display = 'none';
                if (btn) btn.style.display = 'none';
              }
            });
            const opening = menu && menu.style.display !== 'flex';
            if (opening) {
              moreContainer.classList.add('active');
              if (menu) menu.style.display = 'flex';
              moreBtn.style.display = 'none';
            } else {
              moreContainer.classList.remove('active');
              if (menu) menu.style.display = 'none';
              moreBtn.style.display = 'inline-flex';
            }
          });
        }

        if (menu) {
          menu.addEventListener('mouseenter', () => clearTimeout(hideTimeout));
          menu.addEventListener('mouseleave', () => {
            hideTimeout = setTimeout(() => {
              menu.style.display = 'none';
              moreContainer.classList.remove('active');
              moreBtn.style.display = 'none';
            }, 200);
          });
        }

        if (!isRevoked && isOutgoing) {
          const recallBtn = div.querySelector('.recall-btn');
          if (recallBtn) {
            recallBtn.addEventListener('click', () => {
              if (!confirm('Bạn có chắc muốn thu hồi tin nhắn này?')) return;
fetch(`/api/messages/${msg.id}`, { method: 'DELETE', headers: getHeaders(), credentials: 'include' })
                .then(r => { if (r.ok) loadMessages(); });
            });
          }
        }

        if (!isRevoked) {
          const copyBtn = div.querySelector('.copy-btn');
          if (copyBtn) {
            copyBtn.addEventListener('click', () => {
              navigator.clipboard.writeText(msg.message || '').then(() => alert('Đã sao chép tin nhắn'));
            });
          }
        }

        container.appendChild(div);
      });

// --- sau khi append xong tất cả bubbles ---
const shouldScroll = forceScrollBottomOnce;   // chốt trạng thái vào biến cục bộ

if (shouldScroll) {
  // cuộn ngay
  container.scrollTop = container.scrollHeight;
  // cuộn lại sau một "tick" để bắt kịp layout
  setTimeout(() => { container.scrollTop = container.scrollHeight; }, 0);
  // cuộn lại khi MEDIA (img/video) trong tin nhắn load xong
  container.querySelectorAll('img, video').forEach(m => {
    // ảnh <img> dùng 'load', video có thể dùng 'loadedmetadata'
    const ev = m.tagName === 'VIDEO' ? 'loadedmetadata' : 'load';
    m.addEventListener(ev, () => {
      if (shouldScroll) container.scrollTop = container.scrollHeight;
    }, { once: true });
  });
}

// reset cờ (đặt SAU khi đã chốt shouldScroll)
forceScrollBottomOnce = false;


      const distanceFromBottom = container.scrollHeight - container.scrollTop - container.clientHeight;
      if (scrollBtn) scrollBtn.style.display = distanceFromBottom > 100 ? 'flex' : 'none';

      if (!docClickBound) {
        document.addEventListener('click', () => {
          document.querySelectorAll('.more-container.active').forEach(c => {
            c.classList.remove('active');
            const btn = c.querySelector('.more-btn');
            const m = c.querySelector('.context-menu');
            if (m) m.style.display = 'none';
            if (btn) btn.style.display = 'none';
          });
        });
        docClickBound = true;
      }
    })
    .catch(err => console.error('Error loading messages:', err));
}

// ========== SCROLL WATCHER ==========
(function bindMessageScrollWatcher() {
  const container = document.querySelector('.message-area');
  const scrollBtn = document.getElementById('scroll-down-btn');
  if (!container || !scrollBtn) return;
  container.addEventListener('scroll', () => {
    const distanceFromBottom = container.scrollHeight - container.scrollTop - container.clientHeight;
    scrollBtn.style.display = distanceFromBottom > 50 ? 'flex' : 'none';
  });
  scrollBtn.addEventListener('click', () => {
    container.scrollTop = container.scrollHeight;
    scrollBtn.style.display = 'none';
  });
})();

// ===== NEW: đảm bảo có khay preview trước textarea =====
(function ensurePreviewTray() {
  const inputArea = document.querySelector('.input-area');
  if (inputArea && !document.getElementById('preview-tray')) {
    const tray = document.createElement('div');
    tray.id = 'preview-tray';
    tray.className = 'preview-tray';
    inputArea.insertBefore(tray, inputArea.firstChild);
  }
})();

// ===== NEW: render khay preview =====
function renderPreviewTray() {
  const tray = document.getElementById('preview-tray');
  if (!tray) return;

  tray.style.display = selectedFiles.length ? 'flex' : 'none';
  tray.style.gap = '8px';
  tray.style.flexWrap = 'wrap';
  tray.style.alignItems = 'center';
  tray.style.marginBottom = '6px';
  tray.innerHTML = '';

  // Nút thêm (tile +)
  const addTile = document.createElement('button');
  addTile.type = 'button';
  addTile.textContent = '＋';
  addTile.title = 'Thêm ảnh';
  Object.assign(addTile.style, {
    width: '64px', height: '64px', border: '1px dashed #ccc', borderRadius: '8px',
    background: '#fafafa', cursor: 'pointer'
  });
  addTile.addEventListener('click', () => document.getElementById('attach-image').click());
  tray.appendChild(addTile);

  // Ảnh đã chọn
  selectedFiles.forEach((file, idx) => {
    const wrap = document.createElement('div');
    Object.assign(wrap.style, { position: 'relative', width: '64px', height: '64px' });

    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    Object.assign(img.style, { width: '100%', height: '100%', objectFit: 'cover', borderRadius: '8px' });
    img.onload = () => URL.revokeObjectURL(img.src);

   const del = document.createElement('button');
del.type = 'button';
del.title = 'Xóa ảnh này';
Object.assign(del.style, {
  position: 'absolute', top: '-6px', right: '-6px',
  width: '20px', height: '20px',
  border: 'none', borderRadius: '50%',
  background: 'transparent', padding: 0, cursor: 'pointer'
});

// Thêm ảnh SVG vào nút
const delImg = document.createElement('img');
delImg.src = '/images/CloseFill.svg'; // đường dẫn trong public/
delImg.alt = 'Xóa';
Object.assign(delImg.style, {
  width: '100%',
  height: '100%',
  display: 'block'
});

del.appendChild(delImg);

del.addEventListener('click', () => {
  selectedFiles.splice(idx, 1);
  renderPreviewTray();
  refreshSendButtonState();
});


    wrap.appendChild(img);
    wrap.appendChild(del);
    tray.appendChild(wrap);
  });
}

(function initNotice() {
  const overlay = document.getElementById('notice-overlay');
  const modal   = overlay?.querySelector('.ntc-modal');
  const titleEl = document.getElementById('ntc-title');
  const msgEl   = document.getElementById('ntc-message');
  const okBtn   = document.getElementById('ntc-ok');
  const xBtn    = overlay?.querySelector('.ntc-close');
  let lastActive = null;

  function closeNotice() {
    overlay.classList.remove('show');
    overlay.setAttribute('aria-hidden', 'true');
    modal?.classList.remove('ntc-error','ntc-warning','ntc-success');
    // trả focus về nút trước đó (accessibility)
    if (lastActive) { try { lastActive.focus(); } catch {} }
  }

  window.openNotice = function({ 
    title = 'Thông báo', 
    message = '', 
    type = 'default',      // 'error' | 'warning' | 'success' | 'default'
    okText = 'Đã hiểu',
  } = {}) {
    if (!overlay) return console.error('Notice DOM not found!');
    lastActive = document.activeElement;

    titleEl.textContent = title;
    msgEl.textContent   = message;
    okBtn.textContent   = okText;

    modal.classList.remove('ntc-error','ntc-warning','ntc-success');
    if (type !== 'default') modal.classList.add(`ntc-${type}`);

    overlay.classList.add('show');
    overlay.setAttribute('aria-hidden', 'false');
    // focus vào nút OK
    setTimeout(() => okBtn.focus(), 10);
  };

  // Đóng bằng nút, click ra ngoài, và phím ESC
  okBtn?.addEventListener('click', closeNotice);
  xBtn?.addEventListener('click', closeNotice);
  overlay?.addEventListener('click', (e) => { if (e.target === overlay) closeNotice(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && overlay.classList.contains('show')) closeNotice(); });
})();


// ===== NEW: thêm ảnh vào danh sách (ảnh, tối đa 4) =====
function addSelectedImages(fileList) {
  const files = Array.from(fileList).filter(f => f.type?.startsWith('image/'));
  if (!files.length) return;

  const capacity = 4 - selectedFiles.length;
  if (capacity <= 0) {
    openNotice({
      title: 'Lỗi tải lên',
      message: 'Vui lòng chọn tối đa 4 hình',
      type: 'error',
      okText: 'Đã hiểu'
    });
    return;
  }

  if (files.length > capacity) {
    selectedFiles = selectedFiles.concat(files.slice(0, capacity));
    openNotice({
      title: 'Lỗi tải lên',
      message: `Bạn chỉ có thể thêm tối đa ${capacity} ảnh nữa (tổng 4 ảnh).`,
      type: 'warning',
      okText: 'Đã hiểu'
    });
  } else {
    selectedFiles = selectedFiles.concat(files);
  }

  renderPreviewTray();
  refreshSendButtonState();
}



// ===== NEW: bật/tắt nút gửi theo text/ảnh =====
function refreshSendButtonState() {
  const textarea = document.getElementById('message-input');
  const inputArea = textarea.parentElement;
  const hasText = textarea.value.trim().length > 0;
  const hasImages = selectedFiles.length > 0;

  if (hasText || hasImages) inputArea.classList.add('active');
  else inputArea.classList.remove('active');
}

// ========== SEND MESSAGE (text + nhiều ảnh) ==========
async function sendMessage() {
  if (!currentChatId) return;

  const input = document.getElementById('message-input');
  const text = input.value.trim();
  const hasText = !!text;
  const images = [...selectedFiles];

  if (!hasText && images.length === 0) return;

  // 1) Gửi text trước (nếu có)
  if (hasText) {
    const res = await fetch(`/api/chats/${currentChatId}/messages`, {
      method: 'POST',
      headers: getHeaders(),
      credentials: 'include',
      body: JSON.stringify({ message: text })
    });
    if (!res.ok) {
      console.error('Failed to send text message');
      alert('Gửi tin nhắn thất bại!');
      return;
    }
  }

  // 2) Gửi từng ảnh (mỗi ảnh 1 request, song song cho nhanh)
  if (images.length) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const token = localStorage.getItem('sanctum_token') || '';
    const uploads = images.map(file => {
      const fd = new FormData();
      fd.append('file', file);
      return fetch(`/api/chats/${currentChatId}/messages`, {
        method: 'POST',
       headers: { 'X-CSRF-TOKEN': csrf },
        body: fd,
          credentials: 'include'   // <— THÊM DÒNG NÀY
      });
    });
    const results = await Promise.all(uploads);
    const anyFail = results.some(r => !r.ok);
    if (anyFail) {
      console.error('Một hoặc nhiều ảnh gửi thất bại');
      alert('Một số ảnh gửi thất bại!');
    }
  }

  // 3) Reset UI
  input.value = '';
  selectedFiles = [];
  renderPreviewTray();
  refreshSendButtonState();

  // 4) Refresh
  forceScrollBottomOnce = true;
  loadMessages();
  loadConversations();
}

// ========== ATTACH IMAGE BUTTON (chỉ chọn, không upload ngay) ==========
document.getElementById('attach-image').addEventListener('click', () => {
  const picker = document.createElement('input');
  picker.type = 'file';
  picker.accept = 'image/*';
  picker.multiple = true;
  picker.onchange = () => {
    if (picker.files && picker.files.length) addSelectedImages(picker.files);
  };
  picker.click();
});

// ========== INPUT STATE / SEND EVENTS ==========
const textarea = document.getElementById('message-input');
const inputArea = textarea.parentElement;

textarea.addEventListener('input', refreshSendButtonState);
document.getElementById('send-button').addEventListener('click', sendMessage);
document.getElementById('message-input').addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
});

let multiSelectMode = false;
let selectedChatIds = new Set();

const menuBtn = document.getElementById('sidebar-more-btn');
const menu = document.getElementById('sidebar-menu');
const btnMulti = document.getElementById('sb-multi-select');
const btnHideSelected = document.getElementById('sb-hide-selected');
const multiBar = document.getElementById('sb-multibar');
const btnCancel = document.getElementById('sb-cancel');
const btnHide = document.getElementById('sb-hide');

// Toggle menu
menuBtn.addEventListener('click', e => {
  e.stopPropagation();
  menu.classList.toggle('show');
});
document.addEventListener('click', () => menu.classList.remove('show'));

// Bật chọn nhiều
btnMulti.addEventListener('click', () => {
  multiSelectMode = true;
  selectedChatIds.clear();
  document.querySelector('.conversation-list').classList.add('multi');
  updateMultiUI();
  menu.classList.remove('show');
  loadConversations();
});

// Hủy chọn nhiều
btnCancel.addEventListener('click', () => {
  multiSelectMode = false;
  selectedChatIds.clear();
  document.querySelector('.conversation-list').classList.remove('multi');
  updateMultiUI();
  loadConversations();
});

btnHideSelected.addEventListener('click', () => { if(!btnHideSelected.disabled) hideSelected(); });
btnHide.addEventListener('click', () => { if(!btnHide.disabled) hideSelected(); });

// Cập nhật giao diện đếm số
function updateMultiUI(){
  const count = selectedChatIds.size;
  btnHideSelected.textContent = `Ẩn hội thoại (${count})`;
  btnHide.textContent = `Ẩn hội thoại (${count})`;
  btnHideSelected.disabled = count === 0;
  btnHide.disabled = count === 0;
  multiBar.style.display = multiSelectMode ? 'flex' : 'none';
}

// Khi render conversation list
function renderConversationItem(c){
  const div = document.createElement('div');
  div.classList.add('conversation-item');
  if (multiSelectMode) {
    const cb = document.createElement('input');
    cb.type = 'checkbox';
    cb.checked = selectedChatIds.has(c.id);
    cb.addEventListener('click', e => {
      e.stopPropagation();
      cb.checked ? selectedChatIds.add(c.id) : selectedChatIds.delete(c.id);
      updateMultiUI();
    });
    div.appendChild(cb);
  }
  // ... phần còn lại render avatar, name, last message
  return div;
}

async function hideSelected(){
  // Gọi API ẩn hoặc xử lý tạm
  console.log('Ẩn hội thoại', Array.from(selectedChatIds));
  selectedChatIds.clear();
  updateMultiUI();
  // load lại danh sách
}

function showMailboxEmpty(){
  // hiện layout mặc định
  document.querySelector('.no-chat').style.display = 'flex';
  const chatView = document.querySelector('.chat-view');
  chatView.style.display = 'none';
  document.querySelector('.chat-container').classList.remove('chat-open');
}

function closeChat(){
  currentChatId = null;
  showMailboxEmpty();
  if (window.history?.replaceState) {
    history.replaceState(null, '', `/chat`);
  }
}

document.querySelector('.chat-header .back')?.addEventListener('click', closeChat);


// ===== Heartbeat ONLINE =====
let heartbeatTimer = null;
const HB_INTERVAL = 60000; // tạm có thể giảm 10s để test nhanh

function pingHeartbeat(){
  console.log('[HB] ping…');
  return fetch('/api/heartbeat', {
    method: 'POST',
    headers: getHeaders(),
    credentials: 'include'   // bắt buộc cho Sanctum-cookie
  })
  .then(r => r.json().catch(()=>({})))
  .then(d => console.log('[HB] resp', d))
  .catch(e => console.warn('[HB] err', e));
}

function startHeartbeat(){
  stopHeartbeat();
  // gọi ngay 1 lần để thấy trong Network
  pingHeartbeat();
  heartbeatTimer = setInterval(pingHeartbeat, HB_INTERVAL);
}
function stopHeartbeat(){
  if (heartbeatTimer) { clearInterval(heartbeatTimer); heartbeatTimer = null; }
}



// ========== INIT ==========
(async () => {
  await getCurrentUser();
  await loadConversations();

  if (initialChatId) {
    openChat(initialChatId);
  } else {
    showMailboxEmpty(); // <-- quan trọng
  }
  // BẮT ĐẦU HEARTBEAT NGAY
  startHeartbeat();
})();
</script>
</x-layout>
