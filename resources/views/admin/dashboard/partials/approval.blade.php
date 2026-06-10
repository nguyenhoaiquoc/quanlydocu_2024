<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-success shadow-sm">
            <div class="card-header bg-success text-white fw-bold">✅ Đã duyệt</div>
            <ul class="list-group list-group-flush">
                @forelse ($approved as $p)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1">{{ $p->title }}</h6>
                                <small class="text-muted d-block">Người đăng: {{ $p->user->name }}</small>
                                <small class="text-muted d-block">
                                    Danh mục:
                                    @php
                                        $defaultCategories = $p->categories->pluck('name')->toArray();
                                        $customCategory = $p->new_category ? [$p->new_category] : [];
                                        $allCategories = array_merge($defaultCategories, $customCategory);
                                    @endphp

                                    @if (!empty($allCategories))
                                        {{ implode(', ', $allCategories) }}
                                    @else
                                        <span class="text-muted fst-italic">Không có</span>
                                    @endif
                                </small>

                                <small class="text-muted d-block">Giao dịch: {{ $p->deal_type }}</small>
                                <small class="text-muted">Đăng {{ $p->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Không có sản phẩm đã duyệt</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-warning shadow-sm">
            <div class="card-header bg-warning text-white fw-bold">⏳ Chờ duyệt</div>
            <ul class="list-group list-group-flush">
                @forelse ($pending as $p)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1">{{ $p->title }}</h6>
                                <small class="text-muted d-block">Người đăng: {{ $p->user->name }}</small>
                                <small class="text-muted d-block">Danh mục:
                                    {{ $p->categories->pluck('name')->join(', ') }}
                                </small>
                                <small class="text-muted d-block">Giao dịch: {{ $p->deal_type }}</small>
                                <small class="text-muted">Đăng {{ $p->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Không có sản phẩm chờ duyệt</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>