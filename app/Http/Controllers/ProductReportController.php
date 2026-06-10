<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductReportController extends Controller
{
    /**
     * Người dùng gửi báo cáo sản phẩm.
     */
    public function store(Request $request, Product $product)
    {
        Log::info('REPORT STORE HIT', [
            'product_id' => $product->id ?? null,
            'user_id'    => auth()->id(),
            'all'        => $request->all(),
        ]);
        $data = $request->validate([
            'reason'  => 'required|string|max:100',
            'message' => 'nullable|string|max:2000',
        ]);

        // Không cho báo cáo sản phẩm của chính mình (tuỳ bạn)
        if ($product->user_id === Auth::id()) {
            return back()->with('error', 'Bạn không thể báo cáo sản phẩm của chính bạn.');
        }

        $exists = ProductReport::where('product_id', $product->id)
            ->where('reporter_id', Auth::id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Bạn đã báo cáo sản phẩm này rồi.');
        }


        ProductReport::create([
            'product_id'   => $product->id,
            'reporter_id'  => Auth::id(),
            'reason'       => $data['reason'],
            'message'      => $data['message'] ?? null,
            'status'       => 'pending',
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'msg' => 'Đã gửi báo cáo!']);
        }

        return back()->with('success', 'Đã gửi báo cáo sản phẩm! Cảm ơn bạn.');
    }
}
