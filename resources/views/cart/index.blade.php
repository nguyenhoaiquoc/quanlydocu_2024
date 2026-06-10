<x-layout>
    <x-slot:title>Giỏ hàng</x-slot:title>

    <div class="container mt-5">
        <h2 class="section-title">🛒 Giỏ hàng của bạn</h2>

        @if($cartItems->isEmpty())
            <p>Bạn chưa thêm sản phẩm nào vào giỏ.</p>
        @else
            <p class="mb-3">Có <strong>{{ $cartItems->count() }}</strong> sản phẩm trong giỏ.</p>

            <ul class="products row row-cols-1 row-cols-md-3 g-4">
                @foreach($cartItems as $item)
                    <x-product-card :product="$item->product">
                        {{-- Slot cho nút xóa --}}
                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" title="Xóa khỏi giỏ hàng">
                                <i class="bi bi-trash"></i> Xóa
                            </button>
                        </form>
                    </x-product-card>
                @endforeach
            </ul>

        @endif
    </div>

    <x-slot:js>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const items = document.querySelectorAll('ul.products > li');
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('show');
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });

                items.forEach(el => observer.observe(el));
            });
        </script>
    </x-slot:js>
</x-layout>