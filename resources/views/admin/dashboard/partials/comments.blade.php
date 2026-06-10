<div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">💬 Người dùng được đánh giá nhiều</div>
    <ul class="list-group list-group-flush">
        @forelse ($users as $user)
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-1">{{ $user->name }}</h6>
                        <small class="text-muted d-block">Email: {{ $user->email }}</small>
                        <small class="text-muted">Tham gia {{ $user->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="text-end">
                        <div>
                            <span class="badge bg-primary">{{ $user->total_comments }}</span> bình luận
                        </div>
                        <div>
                            <small>Gốc: {{ $user->root_comments }} | Trả lời: {{ $user->reply_comments }}</small>
                        </div>
                        <div class="mt-1">
                            @php $rating = $user->avg_rating ?? 0; @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($rating))
                                    <i class="bi bi-star-fill text-warning"></i>
                                @elseif ($i - $rating < 1)
                                    <i class="bi bi-star-half text-warning"></i>
                                @else
                                    <i class="bi bi-star text-warning"></i>
                                @endif
                            @endfor
                            <small>({{ number_format($rating, 1) }})</small>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted">Không có dữ liệu</li>
        @endforelse
    </ul>
</div>
