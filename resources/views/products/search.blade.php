<x-layout :is-main-layout="true">
    <x-slot:title>Tìm kiếm sản phẩm</x-slot:title>

    <div class="container mt-4">
        <h2 class="section-title">🔍 Kết quả tìm kiếm</h2>
        @php
            $q = request('q');
            $seller = request('seller');
            $min = request('min_price');
            $max = request('max_price');
            $categories = request('categories', []);
            $deal_type = request('deal_type');
            $condition = request('condition');
            $filters = [];

            if ($q) {
                $filters[] = 'tên sản phẩm "<strong>' . e($q) . '</strong>"';
            }
            if ($seller) {
                $filters[] = 'người bán "<strong>' . e($seller) . '</strong>"';
            }
            if ($min && $max) {
                $filters[] = 'giá từ <strong>' . number_format($min, 0, ',', '.') . 'đ</strong> đến <strong>' . number_format($max, 0, ',', '.') . 'đ</strong>';
            } elseif ($min) {
                $filters[] = 'giá từ <strong>' . number_format($min, 0, ',', '.') . 'đ</strong>';
            } elseif ($max) {
                $filters[] = 'giá đến <strong>' . number_format($max, 0, ',', '.') . 'đ</strong>';
            }
            if ($categories) {
                $selectedCategories = \App\Models\Category::whereIn('id', $categories)->pluck('name')->toArray();
                if ($selectedCategories) {
                    $filters[] = 'danh mục "<strong>' . e(implode(', ', $selectedCategories)) . '</strong>"';
                }
            }
            if ($deal_type) {
                $dealTypes = [
                    'price' => 'Giá cố định',
                    'exchange' => 'Trao đổi',
                    'free' => 'Miễn phí'
                ];
                $filters[] = 'loại giao dịch "<strong>' . e($dealTypes[$deal_type] ?? $deal_type) . '</strong>"';
            }
            if ($condition) {
                $filters[] = 'tình trạng "<strong>' . e($condition) . '</strong>"';
            }
        @endphp

        <p class="mb-3">
            Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm
            @if(count($filters))
                cho {!! implode(' và ', $filters) !!}
            @endif
        </p>

        <div class="row">
            {{-- Bên trái: Kết quả sản phẩm --}}
            <div class="col-md-9">
                <ul class="products">
                    @forelse ($products as $product)
                        <x-product-card :product="$product" />
                    @empty
                        <p class="text-muted">Không tìm thấy sản phẩm nào.</p>
                    @endforelse
                </ul>

                {{-- Phân trang --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>

            {{-- Bên phải: Tìm kiếm và Lọc --}}
            <div class="col-md-3">
                <div class="card shadow-sm p-3 mb-3 bg-white rounded">
                    <h5 class="mb-3">🔎 Tìm kiếm & Lọc</h5>

                    <form action="{{ route('products.search') }}" method="GET">
                        <div class="mb-2">
                            <label for="q">Từ khóa:</label>
                            <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Tên sản phẩm...">
                        </div>

                        <div class="mb-2">
                            <label for="seller">Tên người bán:</label>
                            <input type="text" name="seller" id="seller" value="{{ request('seller') }}"
                                class="form-control" placeholder="Người bán...">
                        </div>

                        <div class="mb-2">
                            <label for="deal_type">Loại giao dịch:</label>
                            <select name="deal_type" id="deal_type" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="price" {{ request('deal_type') == 'price' ? 'selected' : '' }}>Giá cố định</option>
                                <option value="exchange" {{ request('deal_type') == 'exchange' ? 'selected' : '' }}>Trao đổi</option>
                                <option value="free" {{ request('deal_type') == 'free' ? 'selected' : '' }}>Miễn phí</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label for="min_price">Giá từ:</label>
                            <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}"
                                class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label for="max_price">Đến:</label>
                            <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}"
                                class="form-control" />
                        </div>

                        <div class="mb-2">
                            <label for="sort">Sắp xếp:</label>
                            <select name="sort" id="sort" class="form-control">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-slot:js>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const items = document.querySelectorAll('ul.products li');
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('show');
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });

                items.forEach(li => observer.observe(li));
            });
        </script>
    </x-slot:js>
</x-layout>