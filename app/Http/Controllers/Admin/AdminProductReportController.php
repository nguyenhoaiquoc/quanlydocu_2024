<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReport;
use App\Models\Product;
use App\Services\ProductNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProductReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $reports = ProductReport::with(['product:id,name,user_id', 'reporter:id,name', 'admin:id,name'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.product-reports.index', compact('reports', 'status'));
    }

    public function show(ProductReport $report)
    {
        $report->load(['product.user', 'reporter', 'admin']);
        return view('admin.product-reports.show', compact('report'));
    }

    public function resolve(Request $request, ProductReport $report)
    {
        $data = $request->validate([
            'resolution_notes' => 'nullable|string|max:2000',
            'status' => 'required|in:resolved,dismissed,reviewing,pending',
        ]);

        $report->update([
            'status'            => $data['status'],
            'resolution_notes'  => $data['resolution_notes'] ?? null,
            'admin_id'          => Auth::id(),
            'resolved_at'       => now(),
        ]);

        // GỬI THÔNG BÁO CHO NGƯỜI BÁO CÁO
        ProductNotificationService::notifyReportStatus($report->fresh());

        return back()->with('success', 'Đã cập nhật trạng thái báo cáo.');
    }


    /**
     * Xóa / Ẩn sản phẩm bị báo cáo (quyết định của admin).
     */
    public function deleteProduct(ProductReport $report)
    {
        $product = Product::find($report->product_id);
        if ($product) {
            // tuỳ bạn: soft delete / đánh dấu cấm / force delete
            $product->delete();
        }

        $report->update([
            'status'      => 'resolved',
            'admin_id'    => Auth::id(),
            'resolved_at' => now(),
            'resolution_notes' => 'Sản phẩm đã bị xóa bởi admin.',
        ]);
        $report->status = 'product_deleted';
        ProductNotificationService::notifyReportStatus($report);

        return back()->with('success', 'Sản phẩm đã bị xóa & báo cáo đã xử lý.');
    }

    public function destroy(ProductReport $report)
    {
        $report->delete();
        return redirect()->route('admin.product-reports.index')
            ->with('success', 'Đã xóa báo cáo.');
    }
}
