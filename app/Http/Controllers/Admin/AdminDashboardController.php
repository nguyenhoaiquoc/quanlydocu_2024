<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function home()
    {
        $dealTypes = Product::select('deal_type', DB::raw('count(*) as total'))
            ->groupBy('deal_type')->pluck('total', 'deal_type');

        return view('admin.home', [
            'totalUsers' => User::count(),
            'totalProducts' => Product::count(),
            'approvedProducts' => Product::where('is_approved', 1)->count(),
            'pendingProducts' => Product::where('is_approved', 0)->count(),
            'totalComments' => Comment::count(),
            'recentProducts' => Product::where('is_approved', 0)->latest()->take(5)->get(),
            'recentUsers' => User::latest()->take(5)->get(),
            'months' => array_map(fn($m) => "Tháng $m", range(1, 12)),
            'productCounts' => array_map(
                fn($m) => Product::whereMonth('created_at', $m)->count(),
                range(1, 12)
            ),
            'dealTypeLabels' => $dealTypes->keys(),
            'dealTypeCounts' => $dealTypes->values(),
        ]);
    }

    public function statistics()
    {
        $months = range(1, 12);
        $productCounts = [];
        $userCounts = [];

        foreach ($months as $month) {
            $productCounts[] = Product::whereMonth('created_at', $month)->count();
            $userCounts[] = User::whereMonth('created_at', $month)->count();
        }

        return view('admin.statistics', [
            'months' => array_map(fn($m) => "Tháng $m", $months),
            'productCounts' => $productCounts,
            'userCounts' => $userCounts,
        ]);
    }
    public function recent()
    {
        return view('admin.recent', [
            'recentProducts' => Product::where('is_approved', 0)->latest()->take(10)->with('user')->get(),
            'recentUsers' => User::latest()->take(10)->get(),
        ]);
    }
   public function loadDetail($type)
{
    switch ($type) {
        case 'users':
            $users = User::withCount(['products', 'comments'])
                ->latest()->take(10)->get();
            return view('admin.dashboard.partials.users', compact('users'));

        case 'products':
            $products = Product::latest()->take(10)->with('user')->get();
            return view('admin.dashboard.partials.products', compact('products'));

        case 'approval':
            $approved = Product::where('is_approved', 1)->latest()->take(5)->get();
            $pending  = Product::where('is_approved', 0)->latest()->take(5)->get();
            return view('admin.dashboard.partials.approval', compact('approved', 'pending'));

        case 'comments':
            $users = User::select('id', 'name', 'email', 'created_at')
                ->get()
                ->map(function ($user) {
                    $userId = $user->id;

                    // Bình luận gốc cho hồ sơ
                    $rootProfileComments = Comment::where('target_user_id', $userId)
                        ->whereNull('parent_id');

                    // Bình luận gốc cho sản phẩm của user
                    $rootProductComments = Comment::whereNull('parent_id')
                        ->whereHas('product', function ($q) use ($userId) {
                            $q->where('user_id', $userId)->where('is_approved', 1);
                        });

                    $rootCount = $rootProfileComments->count() + $rootProductComments->count();

                    // Reply cho hồ sơ
                    $replyToProfileRoots = Comment::whereNotNull('parent_id')
                        ->whereHas('parent', function ($q) use ($userId) {
                            $q->where('target_user_id', $userId);
                        });

                    // Reply cho sản phẩm
                    $replyToProductRoots = Comment::whereNotNull('parent_id')
                        ->whereHas('parent', function ($q) use ($userId) {
                            $q->whereHas('product', function ($p) use ($userId) {
                                $p->where('user_id', $userId)->where('is_approved', 1);
                            });
                        });

                    $replyCount = $replyToProfileRoots->count() + $replyToProductRoots->count();

                    $totalComments = $rootCount + $replyCount;

                    // Tính trung bình rating (chỉ comment gốc có rating)
                    $avgRating = Comment::whereNotNull('rating')
                        ->where(function ($q) use ($userId) {
                            $q->where('target_user_id', $userId)
                                ->orWhereHas('product', function ($p) use ($userId) {
                                    $p->where('user_id', $userId)->where('is_approved', 1);
                                });
                        })
                        ->avg('rating');

                    $user->root_comments = $rootCount;
                    $user->reply_comments = $replyCount;
                    $user->total_comments = $totalComments;
                    $user->avg_rating = $avgRating;

                    return $user;
                })
                ->sortByDesc('total_comments')
                ->take(10)
                ->values();

            return view('admin.dashboard.partials.comments', compact('users'));

        default:
            return '';
    }
}


}
