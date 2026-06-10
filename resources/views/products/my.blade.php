<x-layout>
    <x-slot:title>Bài viết của tôi</x-slot:title>

    <div class="container mt-5">
        <h2 class="mb-4">Danh sách bài viết của bạn</h2>
        <a href="{{ route('products.bin') }}" class="btn btn-outline-secondary mb-4">
            🗑️ Xem thùng rác
        </a>
    </div>
    @if ($products->isEmpty())
        <div class="alert alert-info">Bạn chưa đăng bài viết nào.</div>
    @else
        <ul class="products row row-cols-1 row-cols-md-3 g-4">
            @foreach ($products as $product)
                <x-product-card :product="$product">
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">Chỉnh sửa</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc muốn xóa không?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Xóa</button>
                        </form>
                        @if ($product->updated_at <= now()->subDays(7))
                            <form action="{{ route('products.renew', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success">Gia hạn</button>
                            </form>
                        @endif
                    </div>
                </x-product-card>
            @endforeach
        </ul>
    @endif
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