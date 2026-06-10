<x-admin-layout>
    <h2 class="mb-4">🕒 Sản phẩm chờ duyệt gần đây</h2>

    <ul class="list-group mb-4">
        @forelse ($recentProducts as $product)
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>{{ $product->name }}</strong> <br>
                        <small>
                            Người đăng: <a
                                href="{{ route('admin.users.show', $product->user->id) }}">{{ $product->user->name }}</a> |
                            Loại deal: <span class="text-info">{{ $product->deal_type }}</span> |
                            Danh mục:
                            @if ($product->categories->isNotEmpty() || !empty($product->new_category))
                                {{-- Hiển thị các danh mục có sẵn --}}
                                @if ($product->categories->isNotEmpty())
                                    {{ $product->categories->pluck('name')->join(', ') }}
                                @endif

                                {{-- Thêm new_category nếu có --}}
                                @if (!empty($product->new_category))
                                    @if ($product->categories->isNotEmpty())
                                        ,
                                    @endif
                                    <span class="badge bg-warning text-dark" title="Danh mục do người đăng tự điền">
                                        {{ $product->new_category }}
                                    </span>
                                @endif
                            @else
                                <span class="text-muted fst-italic">Không có</span>
                            @endif
                        </small>
                    </div>
                    <div class="text-muted text-end">
                        {{ $product->created_at->diffForHumans() }}
                        <br>
                        <a href="{{ route('admin.products.edit', $product->id) }}"
                            class="btn btn-sm btn-outline-primary mt-1">✏️ Xem/Sửa</a>
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted">Không có sản phẩm nào gần đây.</li>
        @endforelse
    </ul>

    <h3>👤 Người dùng mới</h3>
    <ul class="list-group">
        @forelse ($recentUsers as $user)
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>{{ $user->name }}</strong> <br>
                        <small>
                            Email: {{ $user->email }} |
                            Role: @php
                                $role = $user->getRoleNames()->first() ?? 'Chưa phân quyền';
                                $badgeClass = match ($role) {
                                    'Admin' => 'warning',
                                    'User' => 'secondary',
                                    default => 'info',
                                };
                            @endphp

                            <span class="badge bg-{{ $badgeClass }}">{{ $role }}</span>

                        </small>
                    </div>
                    <div class="text-muted text-end">
                        {{ $user->created_at->diffForHumans() }}
                        <br>
                        <a href="{{ route('admin.users.show', $user->id) }}"
                            class="btn btn-sm btn-outline-secondary mt-1">👁️ Xem</a>
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted">Không có người dùng nào mới.</li>
        @endforelse
    </ul>
</x-admin-layout>