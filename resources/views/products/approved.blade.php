<x-admin-layout title="Danh sách sản phẩm">
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary m-0">
                <i class="bi bi-box-seam-fill me-2"></i> Danh sách sản phẩm
            </h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th style="width: 250px;">Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Ảnh</th>
                                <th>Danh mục</th>
                                <th>Người đăng</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                    </td>
                                    <td>
                                        @if ($product->deal_type === 'exchange')
                                            <span class="badge bg-info">Trao đổi</span>
                                        @elseif ($product->deal_type === 'free')
                                            <span class="badge bg-success">Miễn phí</span>
                                        @else
                                            <span class="text-danger fw-bold">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $images = json_decode($product->image, true); @endphp
                                        @if ($images)
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($images as $img)
                                                    <img src="{{ asset('images/' . $img) }}" alt="" width="70" height="70" class="rounded border object-fit-cover">
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($product->categories as $category)
                                            <span class="badge bg-secondary">{{ $category->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $product->user->name ?? '-' }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.products.toggle-approve', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-check2-circle me-1"></i> Duyệt
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash3 me-1"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang (nếu có) --}}
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
