<div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">👥 Người dùng mới</div>
    <ul class="list-group list-group-flush">
        @forelse ($users as $user)
            <li class="list-group-item">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        @if(!empty($user->avatar))
                            <img src="{{ asset('images/' . $user->avatar) }}" alt="Avatar" class="rounded-circle me-3"
                                style="width:40px; height:40px; object-fit:cover;">
                        @else
                            <img src="{{ asset('images/default_avatar.png') }}" alt="Avatar" class="rounded-circle me-3"
                                style="width:40px; height:40px; object-fit:cover;">
                        @endif

                        <div>
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <small class="text-muted d-block">{{ $user->email }}</small>
                            <small class="text-muted">Tham gia: {{ $user->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="mb-1">
                            <span class="badge bg-primary">
                                🛒 {{ $user->products_count ?? 0 }}
                            </span> sản phẩm
                        </div>
                        <div>
                            <span class="badge bg-secondary">
                                💬 {{ $user->comments_count ?? 0 }}
                            </span> bình luận
                        </div>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-info mt-1">
                            Chi tiết
                        </a>
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted">Không có người dùng mới</li>
        @endforelse
    </ul>
</div>