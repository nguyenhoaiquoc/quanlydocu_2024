<x-layout>
    <x-slot:title>Thông tin người dùng</x-slot:title>

    <div class="container">
        {{-- ====================== THÔNG TIN NGƯỜI DÙNG ====================== --}}
        <div class="seller-info" style="margin-bottom: 50px">
            <div class="avatar">
                @if($user->image)
                    <img src="{{ asset('images/' . $user->image) }}" alt="Avatar" width="80" />
                @else
                    <img src="{{ asset('images/default_avatar.png') }}" alt="Avatar" width="80" />
                @endif
            </div>

            <div class="info">
                <div class="info-left">
                    <h5 class="d-flex align-items-center gap-2">
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
                    </h5>

                    <div class="status">
                        •
                        @if ($user->last_login_at && $user->last_login_at->diffInMinutes(now()) < 5)
                            <span class="text-success">Đang hoạt động</span>
                        @else
                            Hoạt động {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'chưa rõ' }}
                        @endif

                    </div>

                    <div class="address">
                        {{ $user->address ?? 'Chưa cập nhật địa chỉ' }}
                    </div>

                    {{-- ⭐ trung bình + tổng đánh giá --}}
                    <div class="rating">
                        <span>
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($averageRating))
                                    <i class="bi bi-star-fill text-warning"></i>
                                @elseif ($i - $averageRating < 1)
                                    <i class="bi bi-star-half text-warning"></i>
                                @else
                                    <i class="bi bi-star text-warning"></i>
                                @endif
                            @endfor
                        </span>
                        <div class="reviews">({{ $totalReviews }} đánh giá)</div>
                    </div>
                </div>

                <div class="info-right">
                    <div class="buttons">
                        @php
                            $phone = $user->phone ?? '';
                            $maskedPhone = strlen($phone) > 4
                                ? substr($phone, 0, -4) . '****'
                                : str_repeat('*', max(strlen($phone), 4));
                        @endphp

                        <div class="phone" style="cursor:pointer; user-select:none;" data-full="{{ $phone }}"
                            data-state="masked">
                            📞 <span class="phone-text">{{ $maskedPhone }}</span>
                        </div>


                        {{-- nút Theo dõi / Bỏ theo dõi --}}
                        @auth
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('user.toggle-follow', $user->id) }}" method="POST">
                                    @csrf
                                    @if(auth()->user()->followings->contains($user->id))
                                        <button class="btn-follow btn-secondary">Bỏ theo dõi</button>
                                    @else
                                        <button class="btn-follow btn-primary">Theo dõi</button>
                                    @endif
                                </form>
                            @endif
                        @endauth
                      @auth
  @if(auth()->id() !== $user->id)
    <a href="{{ route('chat.with', $user->id) }}" class="btn-chat">Nhắn tin</a>
  @endif
@endauth

                    </div>

                    <div class="stats">
                        Sản phẩm: <b>{{ $products->count() }}</b> |
                        Người theo dõi: <b>{{ $user->followers->count() }}</b><br>
                        Ngày đăng ký: <b>{{ $user->created_at->format('d/m/Y') }}</b>
                    </div>
                </div>
            </div>
        </div>
        {{-- ====================== TÁC VỤ CỦA NGƯỜI DÙNG ====================== --}}
        @auth
            @if(auth()->id() === $user->id)
                <div class="user-actions mb-4">
                    <h4 class="mb-3 text-primary"><i class="bi bi-tools"></i> Tác vụ của bạn</h4>

                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <a href="{{ route('products.my') }}" class="action-card">
                                <i class="bi bi-file-earmark-text"></i>
                                <span>Bài viết</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="{{ route('favorites.index') }}" class="action-card">
                                <i class="bi bi-heart"></i>
                                <span>Yêu thích</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="{{ route('cart.index') }}" class="action-card">
                                <i class="bi bi-cart"></i>
                                <span>Giỏ hàng</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="{{ route('follows.index') }}" class="action-card">
                                <i class="bi bi-people"></i>
                                <span>Theo dõi</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="{{ route('profile.edit') }}" class="action-card">
                                <i class="bi bi-person-gear"></i>
                                <span>Tài khoản</span>
                            </a>
                        </div>
                    </div>
                </div>

            @endif
        @endauth


        {{-- ====================== GIỚI THIỆU & SẢN PHẨM ====================== --}}
        <div class="seller-intro">
            <h3>Giới thiệu về người dùng</h3>
            <p>
                {{ $user->bio ?? 'Người dùng chưa cập nhật giới thiệu.' }}
            </p>
        </div>

        <div class="product-section-title">
            <h3>Sản phẩm đang bán</h3>
            <p class="subtext">Khám phá các sản phẩm của {{ $user->name }}</p>
        </div>

        <ul class="products">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </ul>

        {{-- ====================== FORM BÌNH LUẬN GỐC ====================== --}}
        <div class="comment-form">
            <h3>Gửi bình luận cho người bán</h3>

            @guest
                <div class="alert alert-warning">
                    Bạn cần <a href="{{ route('login') }}">đăng nhập</a> hoặc <a href="{{ route('register') }}">đăng ký</a>
                    để bình luận.
                </div>
            @else
                <div id="commentError" style="color:red;display:none;"></div>

                <form id="commentForm" action="{{ route('comment.storeForUser', $user) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="comment">Bình luận</label>
                        <textarea id="comment" name="content" placeholder="Viết bình luận của bạn..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Đánh giá</label>
                        <div class="star-rating" id="starRating">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star" data-value="{{ $i }}"></i>
                            @endfor
                        </div>
                    </div>

                    <input type="hidden" name="rating" id="rating" required>
                    <button type="submit">Gửi bình luận</button>
                </form>
            @endguest
        </div>

        {{-- ====================== DANH SÁCH BÌNH LUẬN (đệ quy) ====================== --}}
        <div class="comment-list" id="commentList">
            @foreach(App\Models\Comment::where('target_user_id', $user->id)->whereNull('parent_id')->latest()->take(10)->get() as $comment)
                @include('partials.comment-item', ['comment' => $comment])
            @endforeach
        </div>
    </div>

    <script>




        /*-------------------------------------------------
          0. Cờ đăng nhập
        -------------------------------------------------*/
        const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

        /*-------------------------------------------------
          1. IntersectionObserver: thêm class .show
        -------------------------------------------------*/
        const io = new IntersectionObserver((entries, ob) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('show');
                    ob.unobserve(e.target);
                }
            });
        }, { threshold: .5 });

        ['.seller-info', '.seller-intro', '.product-section-title',
            '.comment-form', '.comment-list', 'ul.products li'].forEach(sel => {
                document.querySelectorAll(sel).forEach(el => io.observe(el));
            });

        /*-------------------------------------------------
          2. Tô màu sao chọn rating
        -------------------------------------------------*/
        const starEls = [...document.querySelectorAll('#starRating i')];
        const ratingInp = document.getElementById('rating');
        starEls.forEach((star, idx) => {
            star.addEventListener('click', () => {
                const val = idx + 1;
                ratingInp.value = val;
                starEls.forEach((s, i) => {
                    s.classList.toggle('active', i < val);
                    s.classList.toggle('bi-star-fill', i < val);
                    s.classList.toggle('bi-star', i >= val);
                });
            });
        });

        /*-------------------------------------------------
          3. Gửi bình luận gốc
        -------------------------------------------------*/
        const formComment = document.getElementById('commentForm');
        formComment?.addEventListener('submit', async e => {
            e.preventDefault();

            if (!isLoggedIn) { location.href = '/login'; return; }   // phòng khi dev quên ẩn form
            const txt = formComment.content.value.trim();
            const star = ratingInp.value.trim();
            if (!txt || !star) { alert('Nhập bình luận và chọn sao!'); return; }

            const res = await fetch(formComment.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: new FormData(formComment)
            });
            if (!res.ok) { alert('Lỗi gửi bình luận'); return; }
            const d = await res.json();
            document.getElementById('commentList')
                .insertAdjacentHTML('afterbegin', renderComment(d));

            formComment.reset();
            ratingInp.value = '';
            starEls.forEach((s, i) => {
                s.classList.remove('active', 'bi-star-fill');
                s.classList.add('bi-star'); // đảm bảo trở về icon sao trống
            });

        });

        /*-------------------------------------------------
          4. Delegation: toggle + gửi reply
        -------------------------------------------------*/
        document.getElementById('commentList').addEventListener('click', e => {
            if (!e.target.classList.contains('reply-btn')) return;
            if (!isLoggedIn) { alert('Hãy đăng nhập để trả lời!'); return; }
            const id = e.target.dataset.id;
            const f = document.querySelector(`form.reply-form[data-parent="${id}"]`);
            if (f) f.style.display = f.style.display === 'none' ? 'block' : 'none';
        });

        document.getElementById('commentList').addEventListener('submit', async e => {
            if (!e.target.classList.contains('reply-form')) return;
            e.preventDefault();
            const form = e.target;
            const pid = form.dataset.parent;
            const txt = form.content.value.trim();
            if (!txt) return;

            const res = await fetch(`/comments/${pid}/reply`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ content: txt })
            });
            if (!res.ok) { alert('Lỗi gửi trả lời'); return; }
            const d = await res.json();
            form.nextElementSibling.insertAdjacentHTML('beforeend', renderComment(d, true));
            form.reset(); form.style.display = 'none';
        });

        /*-------------------------------------------------
          5. Hàm render comment / reply
        -------------------------------------------------*/
        function renderComment(c, isChild = false) {
            let stars = '';
            if (c.rating) {
                stars = '★'.repeat(c.rating) + '☆'.repeat(5 - c.rating);
            }

            return `
<div class="comment-item${isChild ? ' child' : ''}" data-id="${c.id}">
  <div class="comment-author">
    <strong>${c.user}</strong>
    ${stars ? ` – ${stars}` : ''}
  </div>
  <div class="comment-text">${c.content}</div>
  ${isLoggedIn
                    ? `<button class="reply-btn" data-id="${c.id}">Trả lời</button>
           <form class="reply-form" data-parent="${c.id}" style="display:none">
               <textarea name="content" rows="2" placeholder="Nhập trả lời..." required></textarea>
               <button type="submit">Gửi</button>
           </form>`
                    : `<div class="text-muted small">
            Bạn cần <a href="/login">đăng nhập</a> hoặc <a href="/register">đăng ký</a> để trả lời bình luận.
          </div>`
                }
  <div class="replies"></div>
</div>`;
        }


        window.echo.private(`user.${window.APP_USER_ID}`)
  .listen('.comment.created', (e) => {
    commentManager.append({
      id: e.key, read_at: null, created_at: new Date().toISOString(),
      data: {
        type: e.type, user_name: e.user_name, avatar: e.avatar,
        profile_url: e.profile_url, snippet: e.snippet || ''
      }
    });
  });
        /*-------------------------------------------------
          6. Hiển thị list bình luận ngay từ đầu
        -------------------------------------------------*/
        window.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.comment-list')?.classList.add('show');
        });

        // Số điện thoại
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.phone').forEach(function (el) {
                const full = el.dataset.full || '';
                const phoneText = el.querySelector('.phone-text');

                el.addEventListener('click', function () {
                    if (el.dataset.state === 'masked') {
                        phoneText.textContent = full;
                        el.dataset.state = 'revealed';
                    }
                });
            });
        });
    </script>


</x-layout>