<x-admin-layout title="Danh sách người dùng">
    <div class="container py-4">
        <h2 class="text-center mb-4">👥 Danh sách người dùng</h2>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Ngày tạo</th>
                        <th>Số sản phẩm</th>
                        <th>Vai trò</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $user->products_count }}</td>
                            <td>
                                @forelse($user->getRoleNames() as $role)
                                    <span class="badge bg-secondary">{{ $role }}</span>
                                @empty
                                    <span class="text-muted">Chưa phân quyền</span>
                                @endforelse
                            </td>
                            <td>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $users->links() }}
        </div>
    </div>
</x-admin-layout>