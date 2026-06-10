        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>{{ $title ?? 'Trang web bán đồ cũ' }}</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
            <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
            <link rel="stylesheet" href="{{ asset('css/product-detail.css') }}">
            <link rel="stylesheet" href="{{ asset('css/product-card.css') }}">
            <link rel="stylesheet" href="{{ asset('css/info.css') }}">
            <link rel="stylesheet" href="{{ asset('css/chatbox.css') }}">
            <link rel="stylesheet" href="{{ asset('css/allProduct.css') }}">
            <link rel="icon" href="{{ asset('images/logo-fittdc.png') }}" type="image/png">

            <meta name="csrf-token" content="{{ csrf_token() }}">
            @if($isMainLayout)
                <link rel="stylesheet" href="{{ asset('css/home.css') }}">
            @else
                <link rel="stylesheet" href="{{ asset('css/header.css') }}">
            @endif
        </head>

        <body>
            @if($isMainLayout)
                <div class="canvas">
                    <!-- Header mobile -->
<nav class="header-mobile d-flex align-items-center justify-content-between d-lg-none">
    <!-- Hamburger -->
  <button class="hamburger" id="btnMenu" aria-label="Mở menu">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
         xmlns="http://www.w3.org/2000/svg" color="#222222">
        <path d="M20 17C20.5523 17 21 17.4477 21 18C21 18.5523 20.5523 19 20 19H4C3.44772 19 3 18.5523 3 18C3 17.4477 3.44772 17 4 17H20ZM20 11C20.5523 11 21 11.4477 21 12C21 12.5523 20.5523 13 20 13H4C3.44772 13 3 12.5523 3 12C3 11.4477 3.44772 11 4 11H20ZM20 5C20.5523 5 21 5.44772 21 6C21 6.55228 20.5523 7 20 7H4C3.44772 7 3 6.55228 3 6C3 5.44772 3.44772 5 4 5H20Z"
              fill="currentColor"></path>
    </svg>
</button>


    <!-- Logo chữ TDC -->
    <a href="/" class="brand-center">TDC</a>

    <!-- Chuông -->
<a href="javascript:void(0);" class="icon-btn notif-bell">
  <i class="bi bi-bell dot"></i>
</a>
</nav>

<!-- Drawer menu -->
<aside class="mobile-drawer" id="mobileDrawer">
    <div class="drawer-header">
<a href="/"><img src="{{ asset('images/logo-fittdc.png') }}" alt="Logo TDC" class="logo"></a>
        <button id="btnCloseDrawer">&times;</button>
    </div>
    <nav class="drawer-nav">
        <div class="drawer-section">Danh mục</div>
        @foreach($categories as $category)
            <a href="{{ route('categories.user.show', $category->id) }}">{{ $category->name }}</a>
        @endforeach

        @auth
            <div class="drawer-section">Tài khoản</div>
            <a href="{{ route('users.show', ['name' => Auth::user()->name]) }}">Thông tin cá nhân</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 mt-2">Đăng xuất</button>
            </form>
        @else
            <a href="{{ route('login') }}">Đăng Nhập</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}">Đăng Ký</a>
            @endif
        @endauth
    </nav>
</aside>
<div class="drawer-backdrop" id="drawerBackdrop"></div>

                    <nav class="header d-flex justify-content-between align-items-center" id="mainHeader">
                        <div class="header-left d-flex align-items-center">
                            <a href="/"><img src="{{ asset('images/logo-fittdc.png') }}" alt="Logo TDC" class="logo"></a>
                            <div class="dropdown ms-3">
                                <div class="dropdown-label">
                                    Danh Mục <i class="bi bi-chevron-down icon-down"></i>
                                </div>
                                <div class="dropdown-menu">
                                    @foreach($categories as $category)
                                        <a href="{{ route('categories.user.show', $category->id) }}"
                                            style="--i:1">{{ $category->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="header-right d-flex align-items-center">
                            @auth
                                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super-Admin'))
                                    <a href="{{ route('admin.home') }}" class="me-2">Quản lý</a>
                                @else
                                <a href="javascript:void(0);" class="notif-bell" id="notifBell">
  <i class="bi bi-bell dot"></i>
</a>

                                  

                                    <a href="{{ route('products.create') }}" class="me-2">Đăng bán</a>
                                    <a href="{{ route('users.show', ['name' => Auth::user()->name]) }}" class="me-2">Thông tin cá nhân</a>
                                @endif
                            @endauth
                            
                            @if (Route::has('login'))
                                @auth
                                    <div class="dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                            aria-expanded="false" style="color:#333;">
                                            {{ Auth::user()->name }}
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">Log out</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <a href="{{ route('login') }}" class="login-btn">Đăng Nhập</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="login-btn">Đăng Ký</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </nav>

                    @if (session('success') || session('error'))
                        <div class="position-fixed" style="top: 80px; right: 16px; z-index: 9999;">
                            <div class="toast align-items-center text-white 
                                {{ session('success') ? 'bg-success' : 'bg-danger' }} border-0 show fade"
                                role="alert" aria-live="assertive" aria-atomic="true" id="toastAlert">
                                <div class="d-flex">
                                    <div class="toast-body">
                                        {{ session('success') ?? session('error') }}
                                    </div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="homeCarousel" class="carousel slide desktop-only" data-bs-ride="carousel">
                        <div class="carousel-inner">

                            <!-- Slide 1 -->

                            <div class="carousel-item active">
                                <section class="banner">
                                    <div class="banner-content">
                                        <h1>Tìm kiếm đồ cũ giá rẻ – Chất lượng – Sinh viên</h1>
                                        <p>Tiết kiệm chi phí, tìm đồ dùng học tập, đồ gia dụng, và hơn thế nữa!</p>
                                        <a href="{{route('products.all-product')}}#khamphangay">
                                            <button class="cta-button">Khám phá ngay</button>
                                        </a>
                                    </div>
                                </section>
                            </div>
                            <!-- Slide 2 -->
                            <div class="carousel-item">
                                <section class="banner">
                                    <div class="banner-content">
                                        <h1>Ứng dụng mua bán và trao đổi đồ cũ cho sinh viên TDC</h1>
                                        <p>Trao đổi vật dụng cũ - Gắn kết sinh viên, tiết kiệm và bền vững!</p>
                                        <form action="{{ route('products.create') }}">
                                            <button class="cta-button">Đăng bán ngay</button>
                                        </form>
                                    </div>
                                </section>
                            </div>

                            <!-- Slide 3 -->
                            <div class="carousel-item">
                                <section class="banner">
                                    <div class="banner-content">
                                        <h1>Xem các sản phẩm mới nhất</h1>
                                        <p>Những sản phẩm vừa được đăng gần đây nhất</p>
                                        <a href="#noibat">
                                            <button class="cta-button">Xem mới nhất</button>
                                        </a>
                                    </div>
                                </section>
                            </div>
                        </div>

                        <!-- Carousel controls -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>

<!-- Banner mobile -->
<section class="banner-mobile d-lg-none" aria-label="Intro">
  <div class="banner-mobile-bg">
    <h2 class="bm-title">Mua bán đồ cũ giá rẻ – Nhanh chóng – Tiện lợi</h2>
    <!-- nếu muốn thêm 1 dòng mô tả nhỏ -->
    <!-- <p class="bm-sub">"Nhà" mới toanh. Khám phá nhanh!</p> -->
  </div>

  <!-- Ô tìm kiếm nổi ở mép dưới banner -->
  <form action="{{ route('products.all-product') }}" method="GET" class="search-mobile">
    <input type="text" name="q" placeholder="Tìm sản phẩm..." aria-label="Tìm sản phẩm">
    <button type="submit" aria-label="Tìm kiếm"><i class="bi bi-search"></i></button>
  </form>
</section>



                </div>
            @else
                <div class="box" style="margin-bottom: 100px">
                    <nav class="header d-flex justify-content-between align-items-center" id="mainHeader1">
                        <div class="header-left d-flex align-items-center">
                            <a href="/"><img src="{{ asset('images/logo-fittdc.png') }}" alt="Logo TDC" class="logo"></a>
                            <div class="dropdown ms-3">
                                <div class="dropdown-label">
                                    Danh Mục <i class="bi bi-chevron-down icon-down"></i>
                                </div>
                                <div class="dropdown-menu">
                                    @foreach($categories as $category)
                                        <a href="{{ route('categories.user.show', $category->id) }}"
                                            style="--i:1">{{ $category->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="header-right d-flex align-items-center">
                            @auth
                                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super-Admin'))
                                    <a href="{{ route('admin.home') }}" class="me-2">Quản lý</a>
                                @else
                                    <a href="{{ route('chat') }}">
                                        <i class="bi bi-bell dot"></i>
                                    </a>
                                    <a href="{{ route('products.create') }}" class="me-2">Đăng bán</a>
                                    <a href="{{ route('users.show', ['name' => Auth::user()->name]) }}" class="me-2">Thông tin cá nhân</a>
                                @endif
                            @endauth

                            @if (Route::has('login'))
                                @auth
                                    <div class="dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                            aria-expanded="false" style="color:#333;">
                                            {{ Auth::user()->name }}
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">Log out</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <a href="{{ route('login') }}" class="login-btn">Đăng Nhập</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="login-btn">Đăng Ký</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </nav>
                </div>
            @endif
            <div class="container mt-4">
                {{ $slot }}
            </div>
            @if($isMainLayout)
            <footer class="footer">
                <div class="footer-container">
                    <div class="footer-section contact-info">
                        <h2>Liên hệ</h2>
                        <p>KHOA CÔNG NGHỆ THÔNG TIN<br>
                            Trường Cao Đẳng Công Nghệ Thủ Đức</p>
                    </div>
                    <div class="footer-section address-info">
                        <h2>Địa chỉ</h2>
                        <p>53 Võ Văn Ngân, Phường Linh Chiểu, Thành phố Thủ Đức, Thành phố Hồ Chí Minh<br>
                            Điện thoại: 093 239 1272</p>
                    </div>
                    <div class="footer-section about-info">
                        <h2>Về chúng tôi</h2>
                        <p>Website mua bán và trao đổi đồ cũ cho sinh viên TDC –
                            giúp bạn tiếp cận sản phẩm giá rẻ, chất lượng, bảo hành minh bạch.
                        </p>
                    </div>
                    <div class="footer-section social-media">
                        <h2>Kết nối</h2>
                        <a href="https://www.facebook.com/hoaiquoc2005" target="_blank">Facebook</a>
                        <a href="https://id.zalo.me/account?continue=https%3A%2F%2Fchat.zalo.me%2F" target="_blank">Zalo</a>
                        <a href="https://www.instagram.com/_hiqcb/" target="_blank">Instagram</a>
                    </div>
                </div>
                <div class="footer-bottom">
                    &copy; 2025 Nhóm nghiên cứu khoa học TDC - Khoa CNTT
                </div>
            </footer>
            @endif
@if($isMainLayout)
    <!-- Panel thông báo -->
                                    <div id="notificationPanel" class="notif-panel">
                                        <div class="notif-header d-flex justify-content-between align-items-center">
                                            <h5>🔔 Thông báo</h5>
                                            <button id="closeNotif" class="btn btn-sm btn-outline-secondary">Đóng</button>
                                        </div>

                                        <!-- Tabs -->
                                        <ul class="nav nav-tabs" id="notifTabs">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-tab="message">
                                                    Nhắn tin <span id="message-badge" class="badge bg-danger"></span>
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link" data-tab="comment">
                                                    Bình luận <span id="comment-badge" class="badge bg-danger"></span>
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link" data-tab="follow">
                                                    Follow <span id="follow-badge" class="badge bg-danger"></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-tab="product">
                                                    Sản phẩm <span id="product-badge" class="badge bg-danger"></span>
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- Tab Message -->
                                        <div id="tab-message" class="tab-content">
                                            <div class="d-flex justify-content-between align-items-center my-2">
                                                <strong>Tin nhắn đến</strong>
                                                <button id="markAllMessageRead" class="btn btn-sm btn-link text-primary">Đọc tất cả</button>
                                            </div>
                                            <div id="message-loading">Đang tải...</div>
                                            <div id="message-error" class="d-none text-danger">Lỗi tải dữ liệu.</div>
                                            <div id="message-empty" class="d-none text-muted">Không có tin nhắn mới.</div>
                                            <ul id="message-list" class="list-unstyled mt-2"></ul>
                                        </div>

                                        <!-- Tab Comment -->
                                        <div id="tab-comment" class="tab-content d-none">
                                            <div class="d-flex justify-content-between align-items-center my-2">
                                                <strong>Thông báo Bình luận</strong>
                                                <button id="markAllCommentRead" class="btn btn-sm btn-link text-primary">Đọc tất cả</button>
                                            </div>
                                            <div id="comment-loading">Đang tải...</div>
                                            <div id="comment-error" class="d-none text-danger">Lỗi tải dữ liệu.</div>
                                            <div id="comment-empty" class="d-none text-muted">Không có thông báo bình luận.</div>
                                            <ul id="comment-list" class="list-unstyled mt-2"></ul>
                                        </div>

                                        <!-- Tab Follow -->
                                        <div id="tab-follow" class="tab-content d-none">
                                            <div class="d-flex justify-content-between align-items-center my-2">
                                                <strong>Thông báo Follow</strong>
                                                <button id="markAllFollowRead" class="btn btn-sm btn-link text-primary">Đọc tất cả</button>
                                            </div>
                                            <div id="follow-loading">Đang tải...</div>
                                            <div id="follow-error" class="d-none text-danger">Lỗi tải dữ liệu.</div>
                                            <div id="follow-empty" class="d-none text-muted">Không có thông báo follow.</div>
                                            <ul id="follow-list" class="list-unstyled mt-2"></ul>
                                        </div>

                                        <!-- Tab Product -->
                                        <div id="tab-product" class="tab-content d-none">
                                            <div class="d-flex justify-content-between align-items-center my-2">
                                                <strong>Thông báo Sản phẩm</strong>
                                                <button id="markAllProductRead" class="btn btn-sm btn-link text-primary">Đọc tất cả</button>
                                            </div>
                                            <div id="product-loading">Đang tải...</div>
                                            <div id="product-error" class="d-none text-danger">Lỗi tải dữ liệu.</div>
                                            <div id="product-empty" class="d-none text-muted">Không có thông báo sản phẩm.</div>
                                            <ul id="product-list" class="list-unstyled mt-2"></ul>
                                        </div>
                                    </div>
<div id="notifBackdrop" style="position:fixed;inset:0;background:rgba(0,0,0,.25);opacity:0;visibility:hidden;transition:.2s;z-index:900;"></div>
@endif

    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>
          <script>
  Pusher.logToConsole = true; // DEV only
</script>
<script>
  window.Pusher = Pusher;
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const EchoCtor = (window.Echo && window.Echo.default) ? window.Echo.default : window.Echo;

  window.echo = new EchoCtor({
    broadcaster: 'pusher',
    key: '{{ env('PUSHER_APP_KEY') }}',
    cluster: '{{ env('PUSHER_APP_CLUSTER', 'ap1') }}',
    forceTLS: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
      withCredentials: true, // <-- QUAN TRỌNG: gửi cookie kèm request
      headers: {
        'X-CSRF-TOKEN': CSRF,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    }
  });



  window.APP_USER_ID = {{ auth()->id() ?? 'null' }};
</script>





            <script>
    window.appRoutes = {
    message: {
        list: "{{ route('notifications.message') }}",
        read: "{{ route('notifications.message.read', ':id') }}",
        readAll: "{{ route('notifications.message.readAll') }}"
    },
    follow: {
        list: "{{ route('notifications.follow') }}",
        read: "{{ route('notifications.follow.read', ':id') }}",
        readAll: "{{ route('notifications.follow.readAll') }}"
    },
    product: {
        list: "{{ route('notifications.product') }}",
        read: "{{ route('notifications.product.read', ':id') }}",
        readAll: "{{ route('notifications.product.readAll') }}"
    },
    comment: {
        list: "{{ route('notifications.comment') }}",
        read: "{{ route('notifications.comment.read', ':id') }}",
        readAll: "{{ route('notifications.comment.readAll') }}"
    }
    };
    </script>
<script>
const btnMenu = document.getElementById('btnMenu');
const drawer = document.getElementById('mobileDrawer');
const backdrop = document.getElementById('drawerBackdrop');
const btnClose = document.getElementById('btnCloseDrawer');

function openDrawer() {
  drawer.classList.add('open');
  backdrop.classList.add('show');
  btnMenu.classList.add('active');
}
function closeDrawer() {
  drawer.classList.remove('open');
  backdrop.classList.remove('show');
  btnMenu.classList.remove('active');
}

btnMenu.addEventListener('click', () =>
  drawer.classList.contains('open') ? closeDrawer() : openDrawer()
);
backdrop.addEventListener('click', closeDrawer);
if (btnClose) btnClose.addEventListener('click', closeDrawer);


function currentHeaderEl() {
    // <992px dùng header-mobile, ngược lại dùng #mainHeader
    return window.matchMedia('(max-width: 991.98px)').matches
      ? document.querySelector('.header-mobile')
      : document.getElementById('mainHeader');
  }

  function applyScrolled() {
    const el = currentHeaderEl();
    if (!el) return;
    if (window.scrollY > 10) el.classList.add('scrolled');
    else el.classList.remove('scrolled');
  }

  // Lắng nghe cuộn + thay đổi breakpoint
  window.addEventListener('scroll', applyScrolled, { passive: true });
  window.addEventListener('resize', applyScrolled);
  document.addEventListener('DOMContentLoaded', applyScrolled);


  const notifPanel    = document.getElementById('notificationPanel');
  const notifBackdrop = document.getElementById('notifBackdrop');

  function openNotif(e){
    e?.preventDefault();
    notifPanel?.classList.add('show');
    notifBackdrop?.classList.add('show');
    document.body.style.overflow = 'hidden';

    // 👇 THÊM 2 KHỐI NÀY
  // 1) Render cache sẵn (kể cả item realtime vừa append)
  try {
    window.messageManager?.renderCached();
    window.followManager?.renderCached();
    window.productManager?.renderCached();
    window.commentManager?.renderCached();
  } catch(_) {}

  // 2) Merge với server ở nền (không làm mất item realtime)
  try {
    window.messageManager?.fetchData(true, true);
    window.followManager?.fetchData(true, true);
    window.productManager?.fetchData(true, true);
    window.commentManager?.fetchData(true, true);
  } catch(_) {}
  }
  function closeNotif(e){
    e?.preventDefault();
    notifPanel?.classList.remove('show');
    notifBackdrop?.classList.remove('show');
    document.body.style.overflow = '';
  }

  // Gắn 1 lần khi DOM sẵn sàng
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.notif-bell').forEach(btn => {
      btn.addEventListener('click', openNotif, { passive:false });
    });
    document.getElementById('closeNotif')?.addEventListener('click', closeNotif);
    notifBackdrop?.addEventListener('click', closeNotif);
    window.addEventListener('keyup', e => { if (e.key === 'Escape') closeNotif(e); });
  });
</script>


            <script src="{{ asset('js/home.js') }}"></script>



            {{ $js ?? '' }}
        
        </body>

        </html>