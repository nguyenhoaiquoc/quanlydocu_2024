<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🗑️ Thùng rác sản phẩm {{ $isAdminView ? '(Admin)' : '' }}</h2>

        <div class="d-flex gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">🔙 Quay về</a>

            <form action="{{ route($isAdminView ? 'admin.products.empty' : 'products.empty') }}" method="POST"
                onsubmit="return confirm('Bạn có chắc muốn dọn sạch không?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">Dọn sạch</button>
            </form>
        </div>
    </div>

    @if ($products->isEmpty())
        <div class="alert alert-info">Không có sản phẩm nào trong thùng rác.</div>
    @else
        <ul class="products row row-cols-1 row-cols-md-3 g-4">
            @foreach ($products as $product)
                <x-product-card :product="$product" :isAdminView="$isAdminView">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <form action="{{ route($isAdminView ? 'admin.products.restore' : 'products.restore', $product->id) }}"
                            method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-outline-success">Khôi phục</button>
                        </form>

                        <form
                            action="{{ route($isAdminView ? 'admin.products.forceDelete' : 'products.forceDelete', $product->id) }}"
                            method="POST" onsubmit="return confirm('Xóa vĩnh viễn?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Xóa vĩnh viễn</button>
                        </form>
                    </div>
                </x-product-card>
            @endforeach
        </ul>
    @endif
</div>