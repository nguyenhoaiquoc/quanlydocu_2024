<x-admin-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết báo cáo #{{ $report->id }}</h2>
        <a href="{{ route('admin.product-reports.index') }}" class="btn btn-secondary">
            ← Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Thông tin báo cáo</h5>
            <p><strong>Người báo cáo:</strong> {{ $report->reporter?->name ?? 'Không rõ' }}</p>
            <p><strong>Ngày báo cáo:</strong> {{ $report->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Lý do:</strong> {{ $report->reason }}</p>
            <p>
                <strong>Trạng thái:</strong>
                <span class="badge 
                    @if($report->status == 'pending') bg-warning 
                    @elseif($report->status == 'resolved') bg-success 
                    @elseif($report->status == 'dismissed') bg-secondary 
                    @else bg-info @endif">
                    {{ ucfirst($report->status) }}
                </span>
            </p>
            @if($report->admin)
                <p><strong>Người xử lý:</strong> {{ $report->admin->name }}</p>
                @if($report->resolved_at)
                    <p><strong>Ngày xử lý:</strong> {{ $report->resolved_at->format('d/m/Y H:i') }}</p>
                @endif
            @endif
            @if($report->resolution_notes)
                <p><strong>Ghi chú:</strong> {{ $report->resolution_notes }}</p>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Thông tin sản phẩm</h5>
            @if($report->product)
                <p><strong>Tên sản phẩm:</strong> 
                    <a href="{{ route('products.user.show', $report->product->id) }}" target="_blank">
                        {{ $report->product->name }}
                    </a>
                </p>
                <p><strong>Người bán:</strong> {{ $report->product->user->name }}</p>
            @else
                <p class="text-muted">Sản phẩm này đã bị xóa.</p>
            @endif
        </div>
    </div>

    {{-- Form xử lý báo cáo --}}
    <div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Cập nhật trạng thái</h5>

        {{-- Form cập nhật trạng thái (PATCH) --}}
        <form method="POST" action="{{ route('admin.product-reports.resolve', $report->id) }}" class="d-inline-block me-2">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="pending"   @selected($report->status == 'pending')>Chờ xử lý</option>
                    <option value="reviewing" @selected($report->status == 'reviewing')>Đang xem</option>
                    <option value="resolved"  @selected($report->status == 'resolved')>Đã xử lý</option>
                    <option value="dismissed" @selected($report->status == 'dismissed')>Bỏ qua</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="resolution_notes" class="form-label">Ghi chú</label>
                <textarea name="resolution_notes" id="resolution_notes" rows="3" class="form-control">{{ old('resolution_notes', $report->resolution_notes) }}</textarea>
            </div>

            <button type="submit" class="btn btn-success">Cập nhật</button>
        </form>

        {{-- Form xóa sản phẩm bị báo cáo (DELETE) --}}
        @if ($report->product)
            <form method="POST"
                  action="{{ route('admin.product-reports.delete-product', $report->id) }}"
                  class="d-inline-block"
                  onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger ms-2">Xóa sản phẩm</button>
            </form>
        @endif
    </div>
</div>

</x-admin-layout>
