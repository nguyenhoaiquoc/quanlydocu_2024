<x-layout>
    <x-slot:title>👥 Quản lý theo dõi</x-slot:title>

    <div class="container mt-4">
        <h3 class="mb-4 text-primary">👥 Quản lý theo dõi</h3>

        <div class="row g-4">
            <!-- Người bạn đang theo dõi -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">
                        Bạn đang theo dõi ({{ $following->count() }})
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse($following as $followedUser)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <!-- Avatar -->
                                    <a href="{{ route('users.show', $followedUser->name) }}">
                                        @if ($followedUser->image)
                                            <img src="{{ asset('images/' . $followedUser->image) }}" alt="Avatar"
                                                class="rounded-circle me-3 border"
                                                style="width:50px; height:50px; object-fit:cover;">
                                        @else
                                            <img src="{{ asset('images/default_avatar.png') }}" alt="Avatar mặc định"
                                                class="rounded-circle me-3 border"
                                                style="width:50px; height:50px; object-fit:cover;">
                                        @endif
                                    </a>
                                    <div>
                                        <a href="{{ route('users.show', $followedUser->name) }}" class="fw-bold text-decoration-none text-dark">
                                            {{ $followedUser->name }}
                                        </a>
                                        <small class="d-block text-muted">
                                            🛒 {{ $followedUser->products->count() }} sản phẩm |
                                            ⏳ Tham gia {{ $followedUser->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                <form action="{{ route('follows.unfollow', $followedUser->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hủy theo dõi</button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center">Bạn chưa theo dõi ai.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Người theo dõi bạn -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white fw-bold">
                        Người theo dõi bạn ({{ $followers->count() }})
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse($followers as $followerUser)
                            <li class="list-group-item d-flex align-items-center">
                                <a href="{{ route('users.show', $followerUser->name) }}">
                                    @if ($followerUser->image)
                                        <img src="{{ asset('images/' . $followerUser->image) }}" alt="Avatar"
                                            class="rounded-circle me-3 border"
                                            style="width:50px; height:50px; object-fit:cover;">
                                    @else
                                        <img src="{{ asset('images/default_avatar.png') }}" alt="Avatar mặc định"
                                            class="rounded-circle me-3 border"
                                            style="width:50px; height:50px; object-fit:cover;">
                                    @endif
                                </a>
                                <div>
                                    <a href="{{ route('users.show', $followerUser->name) }}" class="fw-bold text-decoration-none text-dark">
                                        {{ $followerUser->name }}
                                    </a>
                                    <small class="d-block text-muted">
                                        🛒 {{ $followerUser->products->count() }} sản phẩm |
                                        ⏳ Tham gia {{ $followerUser->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center">Chưa có ai theo dõi bạn.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layout>
