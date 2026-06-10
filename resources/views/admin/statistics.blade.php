<x-admin-layout>
    <h2 class="mb-4">📊 Biểu đồ thống kê hệ thống</h2>

    <div class="row g-4">
        {{-- Biểu đồ sản phẩm --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header fw-bold bg-info text-white">
                    📦 Sản phẩm được tạo theo tháng
                </div>
                <div class="card-body">
                    <canvas id="productsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Biểu đồ người dùng --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header fw-bold bg-primary text-white">
                    👥 Người dùng mới theo tháng
                </div>
                <div class="card-body">
                    <canvas id="usersChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const productsCtx = document.getElementById('productsChart');
        const usersCtx = document.getElementById('usersChart');

        const months = @json($months);
        const productCounts = @json($productCounts);
        const userCounts = @json($userCounts);

        // Biểu đồ cột sản phẩm
        new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Sản phẩm mới',
                    data: productCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.parsed.y} sản phẩm`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Biểu đồ đường người dùng
        new Chart(usersCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Người dùng mới',
                    data: userCounts,
                    fill: true,
                    tension: 0.4,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(255, 99, 132, 1)',
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.parsed.y} người dùng`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</x-admin-layout>