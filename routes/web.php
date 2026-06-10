<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CartController,
    ProfileController,
    HomeController,
    ProductController,
    CategoryController,
    CommentController,
    CommentNotificationController,
    FavoriteController,
    FollowController,
    FollowNotificationController,
    UserController,
    MessageNotificationController,
    NotificationController,
    ProductNotificationController,
    ProductReportController
};
use App\Http\Controllers\Admin\{
    AdminProductController,
    AdminCategoryController,
    AdminDashboardController,
    AdminProductReportController,
    AdminUserController
};

// ---------------- Dashboard & Auth ----------------
Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');
require __DIR__ . '/auth.php';


// ---------------- Profile ----------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ---------------- Sản phẩm của người dùng ----------------
Route::middleware('auth')->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/my-products', [ProductController::class, 'myProducts'])->name('products.my');
    Route::get('/products/bin', [ProductController::class, 'userBin'])->name('products.bin');
    Route::delete('/products/empty', [ProductController::class, 'empty'])->name('products.empty');
    Route::patch('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::post('/products/{id}/renew', [App\Http\Controllers\ProductController::class, 'reNew'])->name('products.renew');
    Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.forceDelete');
    Route::delete('/products/{product}/images', [ProductController::class, 'deleteImage'])->name('products.deleteImage');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update'); // Đảm bảo route update cho người dùng
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    
});

// ---------------- Trang chủ & Sản phẩm chung ----------------
Route::get('/', [HomeController::class, 'homeDefault'])->name('home');
Route::get('/notifications/product', [NotificationController::class, 'product'])->name('notifications.product');

// load-more

Route::get('/products/load-more', [ProductController::class, 'loadMore'])
    ->name('products.load_more');
Route::post('/notifications/product/{id}/read', [NotificationController::class, 'productRead'])->name('notifications.product.read');
Route::post('/notifications/product/read-all', [NotificationController::class, 'productReadAll'])->name('notifications.product.readAll');
Route::get('/products/all-product', [ProductController::class, 'all'])->name('products.all-product');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.user.show');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.user.show');
Route::get('/users/{name}', [UserController::class, 'show'])->name('users.show');
Route::post('/ai/generate-from-prompt', [UserController::class, 'generateFromPrompt'])->name('ai.generatePrompt');



// ✅ Người dùng chỉ được xem danh mục
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.user.show');

Route::get('/users/{name}', [UserController::class, 'show'])->name('users.show');


// chat box 
Route::middleware('auth')->get('/chat/{chatId?}', function ($chatId = null) {
    return view('users.chat', ['initialChatId' => $chatId]);
})->name('chat');

Route::middleware('auth')->get('/chat/with/{user}', function ($userId) {
    $me = Auth::id();
    $other = (int) $userId;

    if ($me === $other) {
        return back()->with('error', 'Bạn không thể nhắn cho chính mình.');
    }

    $chat = \App\Models\Chat::where(function ($q) use ($me, $other) {
                $q->where('buyer_id', $me)->where('seller_id', $other);
            })
            ->orWhere(function ($q) use ($me, $other) {
                $q->where('buyer_id', $other)->where('seller_id', $me);
            })
            ->first();

    if (!$chat) {
        $chat = \App\Models\Chat::create([
            'buyer_id'  => $me,
            'seller_id' => $other,
        ]);
    }

    return redirect()->route('chat', ['chatId' => $chat->id]);
})->name('chat.with');



// Báo cáo sản phẩm
Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/report', [ProductReportController::class, 'store'])
        ->name('products.report.store');
});

// ---------------- Follow & Yêu thích ----------------
Route::post('/users/{id}/follow', [FollowController::class, 'toggleFollow'])->name('user.toggle-follow');
Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
Route::delete('/favorites/{product}', [FavoriteController::class, 'remove'])->name('favorites.remove');
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

// Quản lý theo dõi
Route::middleware('auth')->group(function () {
    Route::get('/follows', [FollowController::class, 'index'])->name('follows.index');
    Route::delete('/follows/{id}', [FollowController::class, 'unfollow'])->name('follows.unfollow');
});

// ---------------- Bình luận ----------------
Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/comment', [CommentController::class, 'storeForProduct'])->name('comment.storeForProduct');
    Route::post('/users/{user}/comment', [CommentController::class, 'storeForUser'])->name('comment.storeForUser');
    Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comment.reply');
});

// ---------------- Giỏ hàng ----------------
Route::middleware('auth')->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
});

Route::prefix('notifications')->middleware('auth')->group(function () {
    Route::get('message', [MessageNotificationController::class, 'index'])->name('notifications.message');
    Route::post('message/read', [MessageNotificationController::class, 'markRead'])->name('notifications.message.read');
    Route::post('message/read-all', [MessageNotificationController::class, 'markAllRead'])->name('notifications.message.readAll');

    Route::get('comment', [CommentNotificationController::class, 'index'])->name('notifications.comment');
    Route::post('comment/read', [CommentNotificationController::class, 'markRead'])->name('notifications.comment.read');
    Route::post('comment/read-all', [CommentNotificationController::class, 'markAllRead'])->name('notifications.comment.readAll');

    Route::get('follow', [FollowNotificationController::class, 'index'])->name('notifications.follow');
    Route::post('follow/read', [FollowNotificationController::class, 'markRead'])->name('notifications.follow.read');
    Route::post('follow/read-all', [FollowNotificationController::class, 'markAllRead'])->name('notifications.follow.readAll');

    Route::get('product', [ProductNotificationController::class, 'index'])->name('notifications.product');
    Route::post('product/read', [ProductNotificationController::class, 'markRead'])->name('notifications.product.read');
    Route::post('product/read-all', [ProductNotificationController::class, 'markAllRead'])->name('notifications.product.readAll');
});





// ---------------- ADMIN ----------------
Route::middleware(['auth', 'role:Admin|Super-Admin'])
    ->prefix('admin')
    ->as('admin.') // <-- rất quan trọng: tự động thêm tiền tố tên route "admin."
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'home'])->name('home');
        Route::get('dashboard/detail/{type}', [AdminDashboardController::class, 'loadDetail'])->name('dashboard.detail');

        // Dashboard con
        Route::get('statistics', [AdminDashboardController::class, 'statistics'])->name('statistics');
        Route::get('recent', [AdminDashboardController::class, 'recent'])->name('recent');

        // Product routes
        Route::get('products/by-category', [AdminProductController::class, 'byCategory'])->name('products.by-category');
        Route::get('products/by-deal-type', [AdminProductController::class, 'byDealType'])->name('products.by-deal-type');
        Route::get('products/bin', [AdminProductController::class, 'bin'])->name('products.bin');
        Route::delete('products/empty', [AdminProductController::class, 'emptyBin'])->name('products.empty');
        Route::patch('products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{id}/force-delete', [AdminProductController::class, 'forceDelete'])->name('products.forceDelete');
        Route::delete('products/{product}/images', [AdminProductController::class, 'deleteImage'])->name('products.deleteImage');
        Route::get('products/approved', [AdminProductController::class, 'approved'])->name('products.approved');
        Route::patch('products/{id}/toggle-approve', [AdminProductController::class, 'toggleApprove'])->name('products.toggle-approve');
        Route::get('products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{id}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::get('products', [AdminProductController::class, 'index'])->name('products.index');

        // Category CRUD
        Route::resource('categories', AdminCategoryController::class)->names('categories');

        // User management
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        // (đã dùng resource ở trên? Nếu không, bạn giữ custom:)
        Route::get('users/{id}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('users/{id}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('users.toggle-role');
        Route::delete('users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::patch('users/{user}/toggle-honor', [AdminUserController::class, 'toggleHonor'])->name('users.toggle-honor');
        Route::patch('users/{user}/toggle-trust', [AdminUserController::class, 'toggleTrust'])->name('users.toggle-trust');

        // ---------------- Product Reports (Báo cáo sản phẩm) ----------------
        Route::get('product-reports', [AdminProductReportController::class, 'index'])
            ->name('product-reports.index');

        Route::get('product-reports/{report}', [AdminProductReportController::class, 'show'])
            ->name('product-reports.show');

        // Cập nhật trạng thái báo cáo (resolved / dismissed / reviewing)
        Route::patch('product-reports/{report}/resolve', [AdminProductReportController::class, 'resolve'])
            ->name('product-reports.resolve');

        // Xóa sản phẩm bị báo cáo (hành động admin)
        Route::delete('product-reports/{report}/product', [AdminProductReportController::class, 'deleteProduct'])
            ->name('product-reports.delete-product');

        // Xóa bản ghi báo cáo (chỉ xoá record báo cáo, không đụng sản phẩm)
        Route::delete('product-reports/{report}', [AdminProductReportController::class, 'destroy'])
            ->name('product-reports.destroy');
    });

