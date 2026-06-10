<x-layout :is-main-layout="true">
    <div class="container mt-4">
        <div class="search-area">
            <form action="{{ route('products.search') }}" method="GET" class="search-form" id="searchForm">
                <input type="text" name="q" placeholder="Tìm kiếm sản phẩm..." class="search-input"
                    value="{{ request('q') }}" required>
                <button type="submit" class="search-button">Tìm kiếm</button>
            </form>
            <a href="{{ route('products.all-product') }}" class="all-products-btn">Tất cả sản phẩm</a>
        </div>

        <div class="row">
            <div class="col-md-9">
              {{-- 🆕 Sản phẩm mới nhất --}}
@if ($newestProducts->isNotEmpty())
  <h2 class="section-title">🆕 Sản phẩm mới nhất</h2>
  <ul class="products" id="list-newest">
      @foreach ($newestProducts as $product)
          <x-product-card :product="$product" />
      @endforeach
  </ul>
  <div class="text-center mt-3">
      <button
        class="btn btn-outline-primary btn-load-more"
        data-type="newest"
        data-target="#list-newest"
        data-next-page="2"
        data-per-page="4">
        Xem thêm
      </button>
  </div>
@endif

                {{-- Sản phẩm miễn phí --}}
                <h2 class="section-title mt-5" id="free">🆓 Sản phẩm miễn phí</h2>
                @if($freeProducts->isNotEmpty())
                    <ul class="products">
                        @foreach ($freeProducts as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Chưa có sản phẩm miễn phí.</p>
                @endif

                {{-- 💖 Sản phẩm được yêu thích --}}
                <h2 class="section-title mt-5">💖 Sản phẩm được yêu thích</h2>
                @if ($mostFavoritedProducts->isNotEmpty())
                    <ul class="products">
                        @foreach ($mostFavoritedProducts as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Chưa có sản phẩm được yêu thích.</p>
                @endif

            </div>
            <div class="col-md-3">
                <div class="price-filter-card">
                    <h5 class="mb-3">Lọc</h5>
                    <form action="{{ route('products.search') }}" method="GET">
                        <input type="hidden" name="q" value="{{ request('q') }}">

                        <label for="seller">Tên người bán:</label>
                        <input type="text" name="seller" id="seller" class="form-control mb-2"
                            value="{{ request('seller') }}" placeholder="Người bán...">

                        <label for="min_price">Giá từ:</label>
                        <input type="number" name="min_price" id="min_price" class="form-control mb-2"
                            value="{{ request('min_price') }}">

                        <label for="max_price">Đến:</label>
                        <input type="number" name="max_price" id="max_price" class="form-control mb-3"
                            value="{{ request('max_price') }}">

                        <button type="submit" class="btn btn-outline-primary w-100">Lọc</button>
                    </form>
                </div>
                {{-- 🏅 Người được vinh danh --}}
                <div class="card shadow-sm p-3 mb-4 bg-light rounded mt-4">
                    <h5 class="mb-3 text-center">🏅 Người được vinh danh</h5>
                    @forelse ($honoredUsers as $user)
                        <div class="d-flex align-items-center mb-3 p-2 bg-white rounded shadow-sm">
                            <img src="{{ asset('images/' . ($user->image ?? 'default_avatar.png')) }}" alt="Avatar"
                                class="rounded-circle me-3 border" style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <a href="{{ route('users.show', ['name' => $user->name]) }}"
                                    class="fw-bold text-decoration-none text-dark">{{ $user->name }}
                                    <div>
                                        @if ($user->is_honored)
                                            <span class="badge bg-warning" title="Người dùng được vinh danh">🏅Vinh
                                                danh</span>
                                        @endif
                                        @if ($user->is_trusted)
                                            <span class="badge bg-success" title="Người dùng đáng tin cậy"><i
                                                    class="fas fa-shield-alt"></i> Đáng tin cậy</span>
                                        @endif
                                    </div>
                                </a>
                                <div class="text-muted" style="font-size: 14px;">
                                    {{ $user->free_products_count }} sản phẩm miễn phí
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Chưa có người dùng được vinh danh.</p>
                    @endforelse
                </div>

                {{-- 👑 Người dùng đáng tin cậy --}}
                <div class="card shadow-sm p-3 mb-4 bg-light rounded mt-4">
                    <h5 class="mb-3 text-center">👑 Người dùng đáng tin cậy</h5>
                    @forelse ($trustedUsers as $user)
                        <div class="d-flex align-items-center mb-3 p-2 bg-white rounded shadow-sm">
                            <img src="{{ asset('images/' . ($user->image ?? 'default_avatar.png')) }}" alt="Avatar"
                                class="rounded-circle me-3 border" style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <a href="{{ route('users.show', ['name' => $user->name]) }}"
                                    class="fw-bold text-decoration-none text-dark">
                                    {{ $user->name }}
                                    <div>
                                        @if ($user->is_honored)
                                            <span class="badge bg-warning" title="Người dùng được vinh danh">🏅Vinh
                                                danh</span>
                                        @endif
                                        @if ($user->is_trusted)
                                            <span class="badge bg-success" title="Người dùng đáng tin cậy"><i
                                                    class="fas fa-shield-alt"></i> Đáng tin cậy</span>
                                        @endif
                                    </div>
                                </a>
                                <div class="text-muted" style="font-size: 14px;">
                                    {{ $user->approved_products_count }} sản phẩm đã được duyệt
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Chưa có người dùng đáng tin cậy.</p>
                    @endforelse
                </div>

                {{-- ✅ Sản phẩm đã xem gần đây --}}
                <div class="recent-viewed-card">
                    <h5 class="mb-3">👁️ Đã xem gần đây</h5>
                    @if ($viewedProducts->isNotEmpty())
                        @foreach ($viewedProducts as $product)
                            <div class="d-flex mb-3 align-items-center border-bottom pb-2">
                                @php
                                    $images = json_decode($product->image, true);
                                    $firstImage = is_array($images) && count($images) > 0 ? $images[0] : $product->image;
                                    $isFree = $product->deal_type === 'free' && $product->price == 0;
                                @endphp

                                <a href="{{ route('products.user.show', $product->id) }}"
                                    class="d-flex align-items-center text-decoration-none text-dark w-100">
                                    <img src="{{ asset('images/' . $firstImage) }}" alt="{{ $product->name }}"
                                        class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">

                                    <div style="flex: 1">
                                        <div class="fw-bold" style="font-size: 15px;">
                                            {{ $product->name }}
                                            @if($isFree)
                                                <span class="badge bg-success ms-2">Miễn phí</span>
                                            @endif
                                        </div>
                                        <div class="text-muted" style="font-size: 13px;">
                                            🧑 {{ $product->user->name ?? 'Không rõ' }}<br>
                                            💰 {{ $isFree ? '0đ' : number_format($product->price, 0, ',', '.') . 'đ' }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Chưa có sản phẩm đã xem gần đây.</p>
                    @endif
                </div>

                {{-- 🔍 Từ khóa tìm gần đây --}}
                <div class="recent-keywords mt-4">
                    <h5>Từ khóa tìm kiếm gần đây</h5>
                    @if (!empty($recentKeywords))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($recentKeywords as $keyword)
                                <a href="{{ route('products.search', ['q' => $keyword]) }}"
                                    class="badge bg-secondary text-white text-decoration-none px-3 py-2">
                                    {{ $keyword }}
                                </a>
                            @endforeach
                    @else
                            <p class="text-muted">Chưa có từ khóa tìm kiếm gần đây.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot:js>
        <script>

            (function() {
  // Giữ lại IntersectionObserver đang có
  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  function observeNewLis(container) {
    container.querySelectorAll('li:not(.observed)').forEach(li => {
      li.classList.add('observed'); // đánh dấu để không observe lặp
      observer.observe(li);
    });
  }

  // Observe lần đầu
  document.querySelectorAll('ul.products').forEach(ul => observeNewLis(ul));

  async function loadMore(btn) {
    const type      = btn.dataset.type;        // newest|free|favorited
    const targetSel = btn.dataset.target;      // #list-newest ...
    const nextPage  = parseInt(btn.dataset.nextPage || '2', 10);
    const perPage   = parseInt(btn.dataset.perPage || '4', 10);

    const target = document.querySelector(targetSel);
    if (!target) return;

    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Đang tải...';

    try {
      const url = new URL("{{ route('products.load_more') }}", window.location.origin);
      url.searchParams.set('type', type);
      url.searchParams.set('page', nextPage);
      url.searchParams.set('per_page', perPage);

      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      if (!res.ok) throw new Error('Fetch lỗi: ' + res.status);
      const data = await res.json();

      // Gắn HTML mới
      const temp = document.createElement('div');
      temp.innerHTML = data.html.trim();

      // Nếu partial trả về <li> trần:
      temp.querySelectorAll('li, x-product-card, li>*').forEach(node => {
        // an toàn: chỉ append các node hợp lệ
      });

      // Append tất cả children của temp vào <ul>
      while (temp.firstChild) {
        target.appendChild(temp.firstChild);
      }

      // Re-observe các <li> mới để có hiệu ứng .show
      observeNewLis(target);

      if (data.has_more) {
        btn.dataset.nextPage = data.next_page;
        btn.disabled = false;
        btn.textContent = originalText;
      } else {
        btn.textContent = 'Hết';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-outline-secondary');
      }
    } catch (e) {
      console.error(e);
      btn.disabled = false;
      btn.textContent = originalText;
      alert('Tải thêm thất bại. Vui lòng thử lại.');
    }
  }

  // Gán sự kiện cho tất cả nút "Xem thêm"
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-load-more');
    if (btn) {
      e.preventDefault();
      // chống bấm liên tiếp
      if (!btn.disabled) loadMore(btn);
    }
  });
})();

            const header = document.getElementById('mainHeader');
            window.addEventListener('scroll', () => {
                header.classList.toggle('scrolled', window.scrollY > 50);
            });

            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('show');
                        obs.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('ul.products li').forEach(li => observer.observe(li));
        </script>
    </x-slot:js>
</x-layout>