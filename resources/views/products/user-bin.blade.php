@php
    $isAdmin = false;
@endphp

<x-layout>
    <x-bin-product :products="$products" :isAdminView="$isAdmin" />
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
</x-layout>
