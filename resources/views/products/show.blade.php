<x-layout>
    <x-slot:title>Chi tiết sản phẩm</x-slot>
        <script>
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
        </script>

        <div class="container mt-5 pt-5" style="margin-top: 100px !important;">
            <div class="row">
                <!-- Hình ảnh sản phẩm chính -->
                <div class="col-md-6 fade-slide-up">
                    @php
                        $images = json_decode($product->image, true);
                        $firstImage = is_array($images) && count($images) > 0 ? $images[0] : $product->image;
                    @endphp

                    <div class="mb-3 w-100">
                        <img id="mainImage" src="{{ asset('images/' . $firstImage) }}"
                            class="img-fluid rounded shadow-sm w-100 zoom-in" alt="Ảnh sản phẩm"
                            style="max-height: 450px; object-fit: cover;">
                    </div>

                    <!-- Ảnh phụ -->
                    <div class="d-flex gap-2 fade-slide-up">
                        @if (is_array($images))
                            @foreach ($images as $img)
                                <img src="{{ asset('images/' . $img) }}" class="img-thumbnail thumb-img"
                                    style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                    onclick="changeImage(this)">
                            @endforeach
                        @else
                            <img src="{{ asset('images/' . $product->image) }}" class="img-thumbnail thumb-img"
                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                onclick="changeImage(this)">
                        @endif
                    </div>
                </div>

                <!-- Thông tin sản phẩm -->
                <div class="col-md-6  fade-slide-up">
                    <h3 class="fw-bold mb-2">{{ $product->name}}</h3>
                    {{-- Giá --}}
                    <div class="product-price">
                        @if ($product->deal_type === 'exchange')
                            <span class="text-info fw-bold">Trao đổi</span>
                        @elseif ($product->deal_type === 'free')
                            <span class="text-success fw-bold">Miễn phí</span>
                        @else
                            <span class="text-danger fw-bold">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                        @endif
                    </div>
                    <p><i class="bi bi-geo-alt-fill"></i>{{ $product->location_primary }} -
                        {{ $product->location_secondary }}
                    </p>
                    <p><i class="bi bi-clock"></i>
                        Cập nhật lúc
                        {{ $product->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('H:i \n\g\à\y d/m/Y') }}
                    </p>
                    <div class="d-flex gap-3 my-3">
                        @php
                            $phone = $product->user->phone ?? '';
                            $maskedPhone = strlen($phone) > 4
                                ? substr($phone, 0, -4) . '****'
                                : str_repeat('*', max(strlen($phone), 4));
                        @endphp

                        <div class="btn btn-outline-secondary rounded-3 px-4 copy-phone-btn"
                            style="user-select:none; cursor:pointer;" title="Nhấn để hiển thị & sao chép"
                            data-full="{{ $phone }}" data-state="masked">
                            <span class="phone-text">SDT {{ $maskedPhone }}</span>
                        </div>

                        @if(auth()->id() != $product->user->id)


<button 
    class="btn btn-success chat-button" 
    data-product-id="{{ $product->id }}" 
    data-seller-id="{{ $product->user_id }}">
    Chat với người bán
</button>
              
                        @else
                            <a class="btn btn-primary" href="{{ route('chat') }}">📥 Hộp thư đến</a>
                        @endif

                        @if($isInCart)
                            <button class="btn btn-secondary" disabled>
                                🛒 Đã thêm vào giỏ hàng
                            </button>
                        @else
                            <button class="btn btn-warning" onclick="addToCart({{ $product->id }})" id="addToCartBtn">
                                🛒 Thêm vào giỏ hàng
                            </button>
                        @endif
                    </div>

                    <!-- Nút báo cáo + lưu -->
                    <div class="mt-3">
                        @php
                            $hasReported = auth()->check() && \App\Models\ProductReport::where('product_id', $product->id)
                                ->where('reporter_id', auth()->id())
                                ->exists();

                            $isFavorited = auth()->check() && \App\Models\Favorite::where('user_id', auth()->id())
                                ->where('product_id', $product->id)
                                ->exists();
                        @endphp

                        {{-- Nút hoặc text báo cáo --}}
                        @if(!$hasReported)
                            <a href="javascript:void(0)" class="text-decoration-none text-danger me-3"
                                data-bs-toggle="modal" data-bs-target="#reportProductModal">
                                <i class="bi bi-flag"></i> Báo cáo sản phẩm
                            </a>
                        @else
                            <span class="text-muted me-3">
                                <i class="bi bi-flag-fill"></i> Đã báo cáo sản phẩm
                            </span>
                        @endif

                        {{-- Nút yêu thích --}}
                        <a href="javascript:void(0)"
                            class="text-decoration-none {{ $isFavorited ? 'text-danger' : 'text-primary' }}"
                            id="favoriteBtn" onclick="toggleFavorite({{ $product->id }})">
                            <i class="bi {{ $isFavorited ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                            <span id="favoriteText">
                                {{ $isFavorited ? 'Đã lưu vào yêu thích' : 'Lưu vào yêu thích' }}
                            </span>
                            (<span id="favoriteCount">{{ $product->favorites_count }}</span>)
                        </a>
                    </div>

                    <!-- Người bán -->
                    <div class="border rounded-3 p-3 mt-4 d-flex align-items-center justify-content-between bounce-in">
                        <div class="d-flex align-items-center">
                            @if($product->user->image)
                                <img src="{{ asset('images/' . $product->user->image) }}" alt="Avatar"
                                    class="rounded-circle me-3 border"
                                    style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/default_avatar.png') }}" alt="Avatar" width="80" />
                            @endif
                            <div>
                                <strong><a href="{{ route('users.show', ['name' => $product->user->name]) }}">
                                        {{ $product->user->name }}</a> </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin chi tiết bổ sung -->
            <div class="row mt-5 fade-slide-up">
                <div class="col-md-12">
                    <h5 class="fw-bold mb-3">Thông tin chi tiết</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <tbody>
                                <tr>
                                    <th>Danh mục</th>
                                    <td>
                                        @foreach($product->categories as $category)
                                            <span class="badge rounded-pill text-bg-secondary">{{ $category->name }}</span>
                                        @endforeach

                                        @if ($product->new_category)
                                            <span class="badge rounded-pill text-bg-warning"
                                                title="Danh mục này người đăng tự điền.">{{ $product->new_category }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Phương thức thanh toán</th>
                                    <td>{{ $product->payment_method ?? 'Không rõ' }}</td>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tình trạng</th>
                                    <td>{{ $product->condition ?? 'Không rõ' }}</td>
                                </tr>
                                <tr>
                                    <th>Chất liệu</th>
                                    <td>{{ $product->material ?? 'Chưa có' }}</td>
                                </tr>
                                <tr>
                                    <th>Kích thước</th>
                                    <td>{{ $product->size ?? 'Chưa có' }}</td>
                                </tr>
                                <tr>
                                    <th>Thương hiệu</th>
                                    <td>{{ $product->brand ?? 'Khác' }}</td>
                                </tr>
                                <tr>
                                    <th>Thời gian sử dụng</th>
                                    <td>{{ $product->used_duration ?? 'Chưa có' }}</td>

                                </tr>
                                <tr>
                                    <th>Lý do bán</th>
                                    <td>{{ $product->reason ?? 'Chưa cung cấp' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày đăng</th>
                                    <td>{{ $product->created_at->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Mô tả chi tiết</th>
                                    <td>{!! $product->description !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm liên quan -->
            <div class="row mt-5 fade-slide-up">
                <div class="col-md-12">
                    <h5 class="fw-bold mb-3">Sản phẩm tương tự</h5>

                    <div class="related-products-wrapper position-relative">
                        @if ($relatedProducts->count() > 4)
                            <!-- Nút cuộn trái -->
                            <div class="scroll-btn left" onclick="scrollRelated(-1)">
                                <i class="bi bi-chevron-left"></i>
                            </div>
                        @endif

                        <!-- Danh sách sản phẩm -->
                        <div class="related-products-slider d-flex overflow-auto" id="relatedSlider">
                            @foreach ($relatedProducts as $relProduct)
                                <div class="card border-0 related-card me-3" style="min-width: 250px;">
                                    <a href="{{ route('products.user.show', $relProduct->id) }}">
                                        @php
                                            $images = json_decode($relProduct->image, true);
                                            $firstImage = is_array($images) && count($images) > 0 ? $images[0] : $relProduct->image;
                                        @endphp
                                        <img src="{{ asset('images/' . $firstImage) }}" class="card-img-top rounded"
                                            alt="{{ $relProduct->name }}" style="height: 180px; object-fit: cover;">
                                    </a>
                                    <div class="card-body px-0">
                                        @foreach ($relProduct->categories as $category)
                                            <small class="text-muted text-uppercase d-block">{{ $category->name }}</small>
                                        @endforeach

                                        @if ($relProduct->new_category)
                                            <small class="text-warning text-uppercase d-block"
                                                title="Danh mục này người đăng tự điền.">{{ $relProduct->new_category }}</small>
                                        @endif

                                        <a href="{{ route('products.user.show', $relProduct->id) }}"
                                            class="text-decoration-none text-black">
                                            <h6 class="fw-semibold mt-1 mb-1">{{ $relProduct->name }}</h6>
                                        </a>
                                        <p class="fw-bold text-black mb-1" style="font-size: 16px;">
                                            @if ($relProduct->deal_type === 'exchange')
                                                <span class="text-info">Trao đổi</span>
                                            @elseif ($relProduct->deal_type === 'free')
                                                <span class="text-success">Miễn phí</span>
                                            @else
                                                {{ number_format($relProduct->price, 0, ',', '.') }}đ
                                            @endif
                                        </p>
                                        <div class="text-muted small">
                                            <i class="bi bi-person"></i>
                                            <a href="{{ route('users.show', ['name' => $relProduct->user->name]) }}"
                                                class="text-decoration-none text-primary">{{ $relProduct->user->name ?? 'Ẩn danh' }}</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($relatedProducts->count() > 4)
                            <!-- Nút cuộn phải -->
                            <div class="scroll-btn right" onclick="scrollRelated(1)">
                                <i class="bi bi-chevron-right"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @auth
            <form method="POST" action="{{ route('products.report.store', $product->id) }}" id="reportProductForm">
                @csrf
                <div class="modal fade" id="reportProductModal" tabindex="-1" aria-labelledby="reportProductLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="reportProductLabel">Báo cáo sản phẩm</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="reportReason" class="form-label">Lý do</label>
                                    <select class="form-select" name="reason" id="reportReason" required>
                                        <option value="" selected disabled>-- Chọn lý do --</option>
                                        <option value="Sai danh mục hoặc thông tin">Sai danh mục hoặc thông tin</option>
                                        <option value="Giá lừa đảo / Không đúng thực tế">Giá lừa đảo / Không đúng thực tế</option>
                                        <option value="Hàng cấm hoặc không phù hợp">Hàng cấm hoặc không phù hợp</option>
                                        <option value="Tin trùng lặp">Tin trùng lặp</option>
                                        <option value="Thông tin người bán giả mạo">Thông tin người bán giả mạo</option>
                                        <option value="Có dấu hiệu lừa đảo">Có dấu hiệu lừa đảo</option>
                                        <option value="Khác">Khác</option>
                                    </select>
                                    @error('reason')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="reportMessage" class="form-label">Mô tả thêm (tuỳ chọn)</label>
                                    <textarea class="form-control" name="message" id="reportMessage" rows="4"
                                        maxlength="2000" placeholder="Mô tả thêm vấn đề mà bạn gặp phải..."></textarea>
                                    @error('message')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        @endauth

        <script>
            function changeImage(thumb) {
                document.getElementById("mainImage").src = thumb.src;
            }

            // IntersectionObserver chỉ thêm class 'show' 1 lần
            const observer = new IntersectionObserver(
                entries => entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('show');
                        observer.unobserve(e.target);
                    }
                }),
                { threshold: 0.3 }
            );


            const animatedItems = document.querySelectorAll('.fade-slide-up, .zoom-in, .bounce-in');

            animatedItems.forEach(item => {
                observer.observe(item);
            });
        </script>
        <script>
            function scrollRelated(direction) {
                const container = document.getElementById('relatedSlider');
                const card = container.querySelector('.related-card');
                if (card) {
                    const scrollAmount = card.offsetWidth + 16; // 16 = khoảng cách giữa các card
                    container.scrollBy({
                        left: scrollAmount * direction,
                        behavior: 'smooth'
                    });
                }
            }
        </script>

        {{-- Lưu vào mục yêu thích --}}
        <script>
            function toggleFavorite(productId) {
                if (!isLoggedIn) {
                    alert('Vui lòng đăng nhập hoặc đăng ký để lưu vào yêu thích.');
                    return;
                }
                fetch("{{ route('favorites.toggle') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                    .then(res => res.json())
                    .then(data => {
                        const btn = document.getElementById('favoriteBtn');
                        const icon = btn.querySelector('i');
                        const countSpan = document.getElementById('favoriteCount');
                        const textSpan = document.getElementById('favoriteText');

                        if (data.status === 'added') {
                            icon.className = 'bi bi-heart-fill';
                            btn.classList.remove('text-primary');
                            btn.classList.add('text-danger');
                            textSpan.innerText = 'Đã lưu vào yêu thích';
                            countSpan.innerText = parseInt(countSpan.innerText) + 1;
                        } else {
                            icon.className = 'bi bi-heart';
                            btn.classList.remove('text-danger');
                            btn.classList.add('text-primary');
                            textSpan.innerText = 'Lưu vào yêu thích';
                            countSpan.innerText = parseInt(countSpan.innerText) - 1;
                        }
                    })

            }

            function addToCart(productId) {
                if (!isLoggedIn) {
                    alert('Vui lòng đăng nhập hoặc đăng ký để thêm sản phẩm vào giỏ hàng.');
                    return;
                }
                fetch("{{ route('cart.add') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'ok') {
                            const btn = document.getElementById('addToCartBtn');
                            btn.className = 'btn btn-secondary';
                            btn.innerText = '🛒 Đã thêm vào giỏ hàng';
                            btn.disabled = true;

                        } else {
                            alert('Lỗi khi thêm vào giỏ hàng!');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Lỗi khi thêm vào giỏ hàng!');
                    });
            }

            // Số điện thoại
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.copy-phone-btn').forEach(function (el) {
                    const full = el.dataset.full || '';
                    const phoneTextEl = el.querySelector('.phone-text');

                    el.addEventListener('click', async function () {
                        // Hiện full nếu đang che
                        if (el.dataset.state === 'masked') {
                            el.dataset.state = 'revealed';
                            phoneTextEl.textContent = 'SDT ' + full;
                        }

                        // Copy
                        try {
                            await navigator.clipboard.writeText(full);
                        } catch (e) {
                            // fallback cho trình duyệt cũ
                            const tmp = document.createElement('input');
                            tmp.value = full;
                            document.body.appendChild(tmp);
                            tmp.select();
                            document.execCommand('copy');
                            document.body.removeChild(tmp);
                        }
                    });
                });
            });
// Gán sự kiện cho tất cả nút chat
document.querySelectorAll('.chat-button').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const sellerId = this.dataset.sellerId;

        console.log("📩 Tạo chat cho sản phẩm:", productId, "người bán:", sellerId);

        fetch('/api/chats/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                seller_id: sellerId
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log("✅ API trả về:", data);
            if (data.chat_id) {
                window.location.href = `/chat/${data.chat_id}`;
            } else {
                alert("Không tạo được cuộc chat");
            }
        })
        .catch(err => console.error("❌ Lỗi khi gọi API:", err));
    });
});


        </script>

</x-layout>