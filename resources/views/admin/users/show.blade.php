<x-admin-layout title="Chi tiết người dùng">
    <div class="container py-4">
        <h2 class="mb-4">👤 Chi tiết người dùng</h2>

        {{-- Thông tin cơ bản --}}
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Tên:</strong> {{ $user->name }}</li>
            <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
            @if($user->email_verified_at)
                <li class="list-group-item"><strong>Ngày xác thực:</strong>
                    {{ $user->email_verified_at->format('d/m/Y H:i') }}
                </li>
            @endif
            <li class="list-group-item"><strong>Số điện thoại:</strong> {{ $user->phone ?? 'Chưa có' }}</li>
            <li class="list-group-item"><strong>Ngày tạo tài khoản:</strong>
                {{ $user->created_at->format('d/m/Y H:i') }}</li>
            <li class="list-group-item"><strong>Vai trò hiện tại:</strong>
                @foreach ($user->getRoleNames() as $role)
                    <span class="badge bg-info">{{ $role }}</span>
                @endforeach
            </li>
        </ul>

        {{-- Thống kê sản phẩm --}}
        <h5 class="mb-3">📦 Thống kê sản phẩm</h5>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Tổng số sản phẩm:</strong> {{ $user->products->count() }}</li>
            <li class="list-group-item"><strong>Đã duyệt:</strong>
                {{ $user->products->where('is_approved', 1)->count() }}</li>
            <li class="list-group-item"><strong>Chưa duyệt:</strong>
                {{ $user->products->where('is_approved', 0)->count() }}</li>
            <li class="list-group-item"><strong>Miễn phí:</strong>
                {{ $user->products->where('is_approved', 1)->where('deal_type', 'free')->count() }}</li>
            <li class="list-group-item"><strong>Trao đổi:</strong>
                {{ $user->products->where('is_approved', 1)->where('deal_type', 'exchange')->count() }}</li>
            <li class="list-group-item"><strong>Bán giá:</strong>
                {{ $user->products->where('is_approved', 1)->where('deal_type', 'price')->count() }}</li>
        </ul>

        {{-- Thống kê tương tác --}}
        <h5 class="mb-3">📊 Tương tác người dùng</h5>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Số lượt sản phẩm được yêu thích:</strong> {{ $favoriteCount }}</li>
            <li class="list-group-item"><strong>Số bình luận đã gửi:</strong> {{ $user->comments->count() }}</li>
            <li class="list-group-item"><strong>Số người theo dõi họ:</strong> {{ $user->followers->count() }}</li>
            <li class="list-group-item"><strong>Số người họ đang theo dõi:</strong> {{ $user->followings->count() }}
            </li>
        </ul>

        {{-- Nút hành động --}}
        <div class="d-flex gap-3">
            <form action="{{ route('admin.users.toggle-role', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button class="btn btn-warning">
                    🔁 Chuyển vai trò: {{ $user->hasRole('Admin') ? '→ User' : '→ Admin' }}
                </button>
            </form>
            <form action="{{ route('admin.users.toggle-honor', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button class="btn {{ $user->is_honored ? 'btn-outline-warning' : 'btn-warning' }}">
                    🏅 {{ $user->is_honored ? 'Bỏ vinh danh' : 'Vinh danh người dùng' }}
                </button>
            </form>

            <form action="{{ route('admin.users.toggle-trust', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button class="btn {{ $user->is_trusted ? 'btn-outline-success' : 'btn-success' }}">
                    ✅ {{ $user->is_trusted ? 'Bỏ đáng tin cậy' : 'Đánh dấu đáng tin cậy' }}
                </button>
            </form>

            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">🗑️ Xóa tài khoản</button>
            </form>

            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">⬅️ Quay lại</a>
        </div>
    </div>
</x-admin-layout>