<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\UserSearchHistory;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // ======= Trang chủ người dùng =======
    public function homeDefault(Request $request)
    {
        // Bộ lọc giá
        $query = Product::with(['user', 'categories'])
            ->where('is_approved', 1)
            ->orderByDesc('created_at');

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 🆕 Sản phẩm mới nhất
        $newestProducts = Product::with(['user', 'categories'])
            ->where('is_approved', 1)
            ->where('updated_at', '>', now()->subDays(7))
            ->latest()
            ->take(8)
            ->get();

        // 🆓 Sản phẩm miễn phí
        $freeProducts = Product::with(['user', 'categories'])
            ->where('is_approved', 1)
            ->where('deal_type', 'free')
            ->where('price', 0)
            ->latest()
            ->take(8)
            ->get();
        // 🏅 Người dùng được vinh danh
        $honoredUsers = User::where('is_honored', true)
            ->withCount([
                'products as free_products_count' => function ($query) {
                    $query->where('deal_type', 'free')->where('price', 0)->where('is_approved', 1);
                }
            ])
            ->orderByDesc('free_products_count') // sắp xếp theo số lượng
            ->take(5)
            ->get();

        // 👑 Người dùng đáng tin cậy 
        $trustedUsers = User::where('is_trusted', true)
            ->withCount([
                'products as approved_products_count' => function ($query) {
                    $query->where('is_approved', 1);
                }
            ])
            ->orderByDesc('approved_products_count')
            ->take(5)
            ->get();

        // 💖 Sản phẩm được yêu thích nhiều
        $mostFavoritedProducts = Product::with(['user', 'categories'])
            ->where('is_approved', 1)
            ->withCount('favorites')
            ->having('favorites_count', '>', 0)
            ->orderByDesc('favorites_count')
            ->take(8)
            ->get();

        // 👁️ Đã xem gần đây
        $viewedProducts = collect();
        if (session()->has('viewed_products')) {
            $ids = session('viewed_products');
            $viewedProducts = Product::whereIn('id', $ids)
                ->where('is_approved', 1)
                ->with(['user', 'categories'])
                ->get()
                ->sortBy(fn($p) => array_search($p->id, $ids))
                ->values();
        }

        // 🔍 Từ khóa tìm gần đây
        $recentKeywords = [];
        if (auth()->check()) {
            $recentKeywords = UserSearchHistory::where('user_id', auth()->id())
                ->latest()
                ->limit(6)
                ->pluck('keyword');
        }

        return view('home', [
            'newestProducts'           => $newestProducts,
            'freeProducts'             => $freeProducts,
            'mostFavoritedProducts'    => $mostFavoritedProducts,
            'honoredUsers'             => $honoredUsers,
            'trustedUsers'             => $trustedUsers,
            'viewedProducts'           => $viewedProducts,
            'min_price'                => $request->min_price,
            'max_price'                => $request->max_price,
            'recentKeywords'           => $recentKeywords,
        ]);
    }
}
