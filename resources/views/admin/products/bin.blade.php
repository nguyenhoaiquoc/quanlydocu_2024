@extends('layouts.app') {{-- hoặc admin-layout nếu có --}}

@section('content')
<div class="container mt-4">
    <h2>🗑️ Thùng rác sản phẩm (Admin)</h2>

    <form action="{{ route('admin.products.empty') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn dọn sạch toàn bộ?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger mb-3">Dọn sạch</button>
    </form>

    @if ($products->isEmpty())
        <div class="alert alert-info">Không có sản phẩm nào trong thùng rác.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>Người đăng</th>
                    <th>Danh mục</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->user?->name ?? 'Không xác định' }}</td>
                        <td>
                            @foreach ($product->categories as $category)
                                <span class="badge bg-secondary">{{ $category->name }}</span>
                            @endforeach
                        </td>
                        <td class="d-flex gap-2">
                            <form action="{{ route('admin.products.restore', $product->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success">Khôi phục</button>
                            </form>

                            <form action="{{ route('admin.products.forceDelete', $product->id) }}" method="POST"
                                onsubmit="return confirm('Xóa vĩnh viễn sản phẩm này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
