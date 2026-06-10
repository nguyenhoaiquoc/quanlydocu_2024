    <div class="chat-container">
            <div class="sidebar">
  <h2>Chat</h2>

  <div class="search-container">
    <input type="text" id="search-input" placeholder="Nhập 3 ký tự để bắt đầu tìm kiếm">

    <!-- DÙNG CHÍNH NÚT NÀY LÀM TRIGGER MENU -->
    <button id="sidebar-more-btn" class="sb-more-btn">⋮</button>

    <!-- Menu thả xuống đặt ngay đây để position theo search-container -->
    <div id="sidebar-menu" class="sb-menu">
      <button id="sb-multi-select" class="sb-item">Chọn nhiều hội thoại</button>
      <button id="sb-hide-selected" class="sb-item" disabled>Ẩn hội thoại (0)</button>
    </div>
  </div>

  <!-- Danh sách hội thoại -->
  <div class="conversation-list"></div>

  <!-- Thanh đáy khi chọn nhiều -->
  <div id="sb-multibar" class="sb-multibar">
    <button id="sb-cancel" class="sb-cancel">Hủy</button>
    <button id="sb-hide" class="sb-hide" disabled>Ẩn hội thoại (0)</button>
  </div>
</div>

            <div class="main-panel">
                <div class="no-chat">
                    Mẹo! Chat giúp làm sáng tỏ thêm thông tin tăng hiệu quả mua bán
                </div>
                <div class="chat-view">
                <button id="scroll-down-btn" style="
        position:absolute;
        top:400px; /* ngay dưới header */
        left:50%;
        transform:translateX(-50%);
        display:none;
        background:#fff;
        border:1px solid #ddd;
        border-radius:50%;
        width:40px;
        height:40px;
        font-size:20px;
        cursor:pointer;
        align-items:center;
        justify-content:center;
        box-shadow:0 2px 8px rgba(0,0,0,0.15);
        z-index:1000;">⬇</button>


                <div class="chat-header-card">
       <div class="chat-header">
  <button class="back">←</button>

  <a href="#" id="chat-user-link" class="chat-link">
    <img id="chat-avatar" src="" alt="avatar">
    <div class="info">
      <span class="name" id="chat-name"></span>
      
      <span class="status">Hoạt động 1 giờ trước</span>
    </div>
  </a>

  <button class="menu">☰</button>
</div>

        <div class="product-card">
            <img src="https://via.placeholder.com/80" alt="product">
            <div class="info">
                <div class="title">🍎 Iphone 12 ProMax 256 🍎 Không báo ảo 🍎 Góp 0 đồng</div>
                <div class="price">8.500.000 đ</div>
            </div>
        </div>
    </div>

                    <div class="quick-replies"></div>
                    <div class="message-area"></div>
                    <div class="input-area">
                        <div id="preview-tray" class="preview-tray"></div>
  <textarea id="message-input" placeholder="Nhập tin nhắn"></textarea>
  <button class="send" id="send-button">➤</button>
</div>

<div class="input-buttons">
  <button class="attach" id="attach-image">🖼️ Hình và video</button>
</div>

                </div>
            </div>
        </div>

        <!-- Notice Modal -->
<div id="notice-overlay" class="ntc-overlay" aria-hidden="true">
  <div class="ntc-modal" role="dialog" aria-modal="true" aria-labelledby="ntc-title">
    <button class="ntc-close" aria-label="Đóng">×</button>
    <h3 id="ntc-title" class="ntc-title">Thông báo</h3>
    <p id="ntc-message" class="ntc-message">Nội dung thông báo…</p>
    <button id="ntc-ok" class="ntc-btn">Đã hiểu</button>
  </div>
</div>

