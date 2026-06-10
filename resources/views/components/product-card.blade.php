<li>
    <div class="product-item position-relative">
        {{-- Hiển thị trạng thái --}}
        @if ($product->deleted_at !== null)
            <span class="badge position-absolute top-0 start-0 m-1 px-2 py-1 bg-danger text-white" style="font-size: 12px;">
                Đã xóa
            </span>
        @elseif ($product->is_approved === 1)
            <span class="badge position-absolute top-0 start-0 m-1 px-2 py-1 bg-success" style="font-size: 12px;">
                Đã duyệt
            </span>
        @elseif ($product->is_approved === 0)
            <span class="badge position-absolute top-0 start-0 m-1 px-2 py-1 bg-warning text-dark" style="font-size: 12px;">
                Chưa duyệt
            </span>
        @endif

        {{-- Hình ảnh --}}
        <div class="product-top">
            @php
                $images = json_decode($product->image, true);
                $firstImage = is_array($images) && count($images) > 0 ? $images[0] : $product->image;
            @endphp

            @if ($product->is_approved === 1 || ($isAdminView ?? false))
                <a href="{{ route('products.user.show', $product->id) }}" class="product-thumb">
                    <img src="{{ asset('images/' . $firstImage) }}" alt="{{ $product->name }}"
                        style="max-width: 100%; height: auto;">
                </a>
                <a href="{{ route('products.user.show', $product->id) }}" class="buy-now">Xem chi tiết</a>
            @else
                <div class="product-thumb" style="cursor: not-allowed;">
                    <img src="{{ asset('images/' . $firstImage) }}" alt="{{ $product->name }}"
                        style="max-width: 100%; height: auto; opacity: 0.6;">
                </div>
            @endif

        </div>
        
    </div>

    {{-- Thông tin sản phẩm --}}
    <div class="product-info">
         @if ($product->updated_at <= now()->subDays(7))
            <span class="badge  top-0 start-0 m-1 px-2 py-1 bg-warning text-danger">Hết hạn</span>
        @endif
        {{-- Hiển thị các danh mục đã chọn --}}
        @foreach ($product->categories as $category)
            <a href="{{ route('categories.user.show', ['id' => $category->id]) }}" class="product-cat">
                {{ $category->name }}
            </a>
        @endforeach

        {{-- Hiển thị danh mục mới nếu có --}}
        @if ($product->new_category)
            <span class="product-cat pending-category" title="Danh mục này người đăng tự điền.">
                {{ $product->new_category }}
            </span>
        @endif

        {{-- Tên sản phẩm --}}
        @if ($product->is_approved === 1)
            <a href="{{ route('products.user.show', $product->id) }}" class="product-name">
                {{ $product->name }}
            </a>
        @else
            <span class="product-name text-muted">{{ $product->name }}</span>
        @endif

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

        {{-- Người đăng --}}
        <div class="poster">
            <strong>
                <a href="{{ route('users.show', ['name' => $product->user->name]) }}">
                    <i class="bi bi-person">: </i> {{ $product->user?->name ?? 'Không xác định' }}
                </a>
            </strong>
        </div>
    </div>

    {{-- Slot cho nút hành động --}}
    @if (isset($slot) && trim($slot) != '')
        <div class="mt-2 text-center">
            {{ $slot }}
        </div>
    @endif
</li>