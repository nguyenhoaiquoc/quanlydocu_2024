<x-layout>
    <x-slot:title>{{ $category->name }}</x-slot>

    <div class="container mt-4">
        <h1 class="mb-4">{{ $category->name }}</h1>

        <p class="mb-3">
    Tìm thấy <strong>{{ $category->products->count() }}</strong> sản phẩm
    @if(request('q') || request('seller') || request('min_price') || request('max_price'))
        @php $filters = []; @endphp

        @if(request('q'))
            @php $filters[] = 'tên sản phẩm "<strong>' . e(request('q')) . '</strong>"'; @endphp
        @endif

        @if(request('seller'))
            @php $filters[] = 'người bán "<strong>' . e(request('seller')) . '</strong>"'; @endphp
        @endif

        @if(request('min_price') || request('max_price'))
            @php
                $min = request('min_price') ? number_format(request('min_price')) : '0';
                $max = request('max_price') ? number_format(request('max_price')) : '∞';
                $filters[] = 'giá từ <strong>' . $min . 'đ</strong> đến <strong>' . $max . 'đ</strong>';
            @endphp
        @endif

        cho {!! implode(' và ', $filters) !!}
    @endif
</p>


        <div class="row">
            {{-- Danh sách sản phẩm --}}
            <div class="col-md-9">
                <ul class="products">
                    @forelse ($category->products as $product)
                        <x-product-card :product="$product" />
                    @empty
                        <p class="text-muted">Không tìm thấy sản phẩm nào.</p>
                    @endforelse
                </ul>
            </div>

            {{-- Bộ lọc --}}
            <div class="col-md-3">
                <div class="price-filter-card">
                    <h5 class="mb-3">🔍 Tìm kiếm & lọc</h5>
                    <form action="{{ route('categories.user.show', ['id' => $category->id]) }}" method="GET">
                        <div class="mb-2">
                            <label for="q" class="form-label">Tên sản phẩm:</label>
                            <input type="text" name="q" id="q" class="form-control"
                                   value="{{ request('q') }}" placeholder="VD: bàn học, laptop...">
                        </div>

                        <div class="mb-2">
                            <label for="seller" class="form-label">Tên người bán:</label>
                            <input type="text" name="seller" id="seller" class="form-control"
                                   value="{{ request('seller') }}" placeholder="VD: Quốc, Huy...">
                        </div>

                        <div class="mb-2">
                            <label for="min_price" class="form-label">Giá từ:</label>
                            <input type="number" name="min_price" id="min_price" class="form-control"
                                   value="{{ request('min_price') }}">
                        </div>

                        <div class="mb-3">
                            <label for="max_price" class="form-label">Đến:</label>
                            <input type="number" name="max_price" id="max_price" class="form-control"
                                   value="{{ request('max_price') }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Áp dụng</button>
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
