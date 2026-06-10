<x-layout>
    <x-slot:title>Sản phẩm yêu thích</x-slot:title>

    <div class="container mt-5">
        <h2>Sản phẩm bạn đã yêu thích</h2>

        @if ($favorites->isEmpty())
            <p>Bạn chưa yêu thích sản phẩm nào.</p>
        @else
            <ul class="products">
                @foreach ($favorites as $fav)
                    <x-product-card :product="$fav->product">
                        {{-- Bỏ yêu thích (hiện trong mỗi card) --}}
                        <form action="{{ route('favorites.remove', $fav->product_id) }}" method="POST"
                            onsubmit="return confirm('Bạn chắc chắn muốn bỏ yêu thích?')"
                            style="position: absolute; top: 10px; right: 10px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-x-lg"></i> Bỏ yêu thích
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