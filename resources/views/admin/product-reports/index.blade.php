<x-admin-layout>
    <h1 class="mb-4">Báo cáo sản phẩm</h1>

    <form class="mb-3">
        <select name="status" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
            <option value="">Tất cả</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Chờ xử lý</option>
            <option value="reviewing" {{ request('status')=='reviewing'?'selected':'' }}>Đang xem</option>
            <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Đã xử lý</option>
            <option value="dismissed" {{ request('status')=='dismissed'?'selected':'' }}>Bỏ qua</option>
        </select>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Người báo cáo</th>
                        <th>Lý do</th>
                        <th>Trạng thái</th>
                        <th>Ngày</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>
                                @if($r->product)
                                    <a href="{{ route('products.user.show', $r->product_id) }}" target="_blank">
                                        {{ $r->product->name }}
                                    </a>
                                @else
                                    (đã xóa)
                                @endif
                            </td>
                            <td>{{ $r->reporter?->name }}</td>
                            <td>{{ $r->reason }}</td>
                            <td>
                                <span class="badge 
                                    @if($r->status=='pending') bg-warning 
                                    @elseif($r->status=='resolved') bg-success 
                                    @elseif($r->status=='dismissed') bg-secondary 
                                    @else bg-info @endif">
                                    {{ $r->status }}
                                </span>
                            </td>
                            <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.product-reports.show', $r->id) }}" class="btn btn-sm btn-primary">
                                    Xem
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có báo cáo nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
