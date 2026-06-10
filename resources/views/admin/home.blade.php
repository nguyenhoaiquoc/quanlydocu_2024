<x-admin-layout>
    <div class="container-fluid">
        <h2 class="mb-4">📊 Tổng quan hệ thống</h2>

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            @php
                $stats = [
                    ['color' => 'primary', 'icon' => 'bi-people', 'label' => 'Người dùng', 'value' => $totalUsers, 'id' => 'users'],
                    ['color' => 'success', 'icon' => 'bi-box', 'label' => 'Sản phẩm', 'value' => $totalProducts, 'id' => 'products'],
                    ['color' => 'warning', 'icon' => 'bi-check-circle', 'label' => 'Đã duyệt / Chờ', 'value' => "$approvedProducts / $pendingProducts", 'id' => 'approval'],
                    ['color' => 'danger', 'icon' => 'bi-chat-dots', 'label' => 'Bình luận', 'value' => $totalComments, 'id' => 'comments'],
                ];
            @endphp
            @foreach ($stats as $stat)
                <div class="col-md-3">
                    <div class="card text-white bg-{{ $stat['color'] }} shadow-sm h-100 stat-card" style="cursor:pointer;"
                        data-id="{{ $stat['id'] }}">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi {{ $stat['icon'] }} display-6 me-3"></i>
                            <div>
                                <h6 class="mb-1">{{ $stat['label'] }}</h6>
                                <h3 class="mb-0">{{ $stat['value'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Dynamic detail section --}}
        <div id="dynamic-section" class="mt-4"></div>
        @push('scripts')
            <script>
                document.querySelectorAll('.stat-card').forEach(card => {
                    card.addEventListener('click', function () {
                        const type = this.getAttribute('data-id');
                        fetch(`{{ url('admin/dashboard/detail') }}/${type}`)
                            .then(res => res.text())
                            .then(html => {
                                document.getElementById('dynamic-section').innerHTML = html;
                            });
                    });
                });
            </script>

        @endpush

</x-admin-layout>