<div class="card">
    <div class="card-header bg-light fw-bold">📦 Sản phẩm mới</div>
    <ul class="list-group list-group-flush">
        @forelse ($products as $product)
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-1">{{ $product->name }}</h6>
                        <small class="text-muted">
                            Người đăng: {{ $product->user->name }} •
                            {{ $product->created_at->diffForHumans() }}
                        </small><br>
                        <small>
                            Trạng thái:
                            @if($product->is_approved)
                                <span class="badge bg-success">Đã duyệt</span>
                            @else
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @endif
                        </small>
                    </div>
                    <div class="text-end">
                        @if ($product->deal_type === 'price' && $product->price)
                            <strong class="text-primary">{{ number_format($product->price) }}₫</strong>
                        @elseif ($product->deal_type === 'exchange')
                            <span class="badge bg-info text-dark">Trao đổi</span>
                        @elseif ($product->deal_type === 'free')
                            <span class="badge bg-success">Miễn phí</span>
                        @endif
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted">Không có sản phẩm nào</li>
        @endforelse
    </ul>
</div>