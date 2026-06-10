<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/product-card.css') }}">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            background: linear-gradient(to right, #550372, #036271);
            color: #94a3b8;
            min-height: 100vh;
        }

        .sidebar a.nav-link {
            color: #94a3b8;
            border-radius: 8px;
            margin-bottom: 4px;
            padding: 10px 15px;
            transition: 0.3s ease;
            font-weight: 500;
        }

        .sidebar a.nav-link:hover,
        .sidebar a.nav-link.active {
            background-color: #1d4ed8;
            color: #fff;
        }

        .sidebar .nav-heading {
            font-size: 11px;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 5px;
            color: #64748b;
            padding-left: 15px;
        }

        .sidebar-logo {
            text-align: center;
            padding: 20px;
        }

        .sidebar-logo img {
            max-width: 80px;
            border-radius: 8px;
        }

        main {
            padding: 30px;
        }

        footer {
            background-color: #ffffff;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
@stack('scripts')

<body>
    @php
    use App\Models\ProductReport;
    $pendingProductReports = ProductReport::where('status', 'pending')->count();
@endphp

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar d-flex flex-column p-0">
                <div class="sidebar-logo">
                    <a href="/">
                        <img src="{{ asset('images/logo-fittdc.png') }}" alt="Trang chủ">
                    </a>
                </div>

                <ul class="nav flex-column px-3">
                    <!-- Quản lý hệ thống -->
                    <div class="nav-heading">Quản lý hệ thống</div>
                    <li class="nav-item">
                        <a href="{{ route('admin.home') }}" class="nav-link {{ Request::is('admin') ? 'active' : '' }}">
                            <i class="bi bi-house-door me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.statistics') }}"
                            class="nav-link {{ Request::is('admin/statistics') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-line me-2"></i> Biểu đồ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.recent') }}"
                            class="nav-link {{ Request::is('admin/recent') ? 'active' : '' }}">
                            <i class="bi bi-clock-history me-2"></i> Gần đây
                        </a>
                    </li>

                    <!-- Categories -->
                    <div class="nav-heading">Quản lý danh mục</div>
                    <li>
                        <a href="#categoriesSubmenu" data-bs-toggle="collapse"
                            class="nav-link d-flex justify-content-between align-items-center {{ Request::is('admin/categories*') ? 'active' : '' }}">
                            <span><i class="bi bi-grid me-2"></i> Danh mục</span>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse {{ Request::is('admin/categories*') ? 'show' : '' }}"
                            id="categoriesSubmenu">
                            <ul class="nav flex-column ms-3">
                                <li>
                                    <a href="{{ route('admin.categories.index') }}"
                                        class="nav-link {{ Request::is('admin/categories') ? 'active' : '' }}">
                                        <i class="bi bi-list me-2"></i> Xem tất cả
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.categories.create') }}"
                                        class="nav-link {{ Request::is('admin/categories/create') ? 'active' : '' }}">
                                        <i class="bi bi-plus-square me-2"></i> Thêm danh mục
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <!-- Products -->
                    <div class="nav-heading">Quản lý sản phẩm</div>
                    <li>
                        <a href="#productsSubmenu" data-bs-toggle="collapse" class="nav-link d-flex justify-content-between align-items-center
               {{ Request::is('admin/products*') || Request::is('admin/product-reports*') ? 'active' : '' }}">
                            <span><i class="bi bi-box me-2"></i> Sản phẩm</span>
                            <i class="bi bi-chevron-down"></i>
                        </a>

                        <div class="collapse
        {{ Request::is('admin/products*') || Request::is('admin/product-reports*') ? 'show' : '' }}"
                            id="productsSubmenu">

                            <ul class="nav flex-column ms-3">
                                <li>
                                    <a href="{{ route('admin.products.index') }}"
                                        class="nav-link {{ Request::is('admin/products') ? 'active' : '' }}">
                                        <i class="bi bi-list me-2"></i> Xem tất cả
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.products.approved') }}"
                                        class="nav-link {{ Request::is('admin/products/approved') ? 'active' : '' }}">
                                        <i class="bi bi-check2-circle me-2"></i> Duyệt sản phẩm
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.products.by-category') }}"
                                        class="nav-link {{ Request::is('admin/products/by-category') ? 'active' : '' }}">
                                        <i class="bi bi-diagram-3 me-2"></i> Theo danh mục
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.products.by-deal-type') }}"
                                        class="nav-link {{ Request::is('admin/products/by-deal-type') ? 'active' : '' }}">
                                        <i class="bi bi-tags me-2"></i> Loại giao dịch
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.products.bin') }}"
                                        class="nav-link {{ Request::is('admin/products/bin') ? 'active' : '' }}">
                                        <i class="bi bi-trash me-2"></i> Thùng rác
                                    </a>
                                </li>

                                {{-- NEW: Product Reports --}}
                                <li>
                                    <a href="{{ route('admin.product-reports.index') }}"
                                        class="nav-link {{ Request::is('admin/product-reports*') ? 'active' : '' }}">
                                        <i class="bi bi-flag me-2"></i> Báo cáo sản phẩm
                                        @if($pendingProductReports)
                                            <span class="badge bg-danger ms-1">{{ $pendingProductReports }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Users -->
                    <div class="nav-heading">Quản lý người dùng</div>
                    <li>
                        <a href="#usersSubmenu" data-bs-toggle="collapse"
                            class="nav-link d-flex justify-content-between align-items-center {{ Request::is('admin/users*') ? 'active' : '' }}">
                            <span><i class="bi bi-people me-2"></i> Người dùng</span>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse {{ Request::is('admin/users*') ? 'show' : '' }}" id="usersSubmenu">
                            <ul class="nav flex-column ms-3">
                                <li>
                                    <a href="{{ route('admin.users.index') }}"
                                        class="nav-link {{ Request::is('admin/users') ? 'active' : '' }}">
                                        <i class="bi bi-list me-2"></i> Xem tất cả
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-10 p-0 d-flex flex-column">
                <main class="flex-grow-1">
                    {{ $slot }}
                </main>
                <footer class="text-center py-3 border-top">
                    <p class="mb-0">&copy; FIT-TDC. All rights reserved.</p>
                </footer>
            </div>
        </div>
    </div>

    {{ $js ?? '' }}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>