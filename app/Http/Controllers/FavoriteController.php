<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\ProductNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $user      = Auth::user();
        $productId = $request->input('product_id');

        $product = Product::findOrFail($productId);

        $existing = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed']);
        }

        // Thêm yêu thích
        Favorite::create([
            'user_id'    => $user->id,
            'product_id' => $productId,
        ]);

        // Tạo thông báo cho chủ sản phẩm (trừ trường hợp tự thích)
        if ($product->user_id !== $user->id) {
            ProductNotification::firstOrCreate([
                'user_id'    => $product->user_id,
                'actor_id'   => $user->id,
                'product_id' => $product->id,
                'type'       => 'product_favorited',
            ]);
        }


        return response()->json(['status' => 'added']);
    }

    public function index()
    {
        $favorites = Favorite::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    public function remove($productId)
    {
        $favorite = Favorite::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return back()->with('success', 'Đã bỏ yêu thích sản phẩm.');
        }

        return back()->with('error', 'Không tìm thấy sản phẩm trong danh sách yêu thích.');
    }
}
