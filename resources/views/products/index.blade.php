<x-admin-layout title="Danh sách sản phẩm">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">📦 Danh sách sản phẩm</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive shadow rounded overflow-hidden">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#ID</th>
                        <th scope="col">Tên sản phẩm</th>
                        <th scope="col">Giá</th>
                        <th scope="col">Ảnh</th>
                        <th scope="col">Danh mục</th>
                        <th scope="col">Người đăng</th>
                        <th scope="col" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td style="max-width: 300px;">
                                <strong>{{ $product->name }}</strong>
                            </td>
                            <td class="text-danger fw-bold">
                                <div class="product-price">
                                    @if ($product->deal_type === 'exchange')
                                        <span class="text-info fw-bold">Trao đổi</span>
                                    @elseif ($product->deal_type === 'free')
                                        <span class="text-success fw-bold">Miễn phí</span>
                                    @else
                                        <span class="text-danger fw-bold">{{ number_format($product->price, 0, ',', '.') }}
                                            đ</span>
                                    @endif
                                </div>
                            </td>
                            @php
                                $images = json_decode($product->image, true);
                                $firstImage = is_array($images) && count($images) > 0 ? $images[0] : null;
                            @endphp

                            <td style="width: 120px;">
                                @if ($firstImage && file_exists(public_path('images/' . $firstImage)))
                                    <img src="{{ asset('images/' . $firstImage) }}" alt="Ảnh sản phẩm"
                                        class="img-fluid rounded border"
                                        style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                @else
                                    <span class="text-muted">Không có ảnh</span>
                                @endif
                            </td>
                            <td>
                                @foreach($product->categories as $category)
                                    <span class="badge bg-info text-dark me-1">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>{{ $product->user->name ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="btn btn-sm btn-outline-warning me-1">
                                    ✏️ Sửa
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>