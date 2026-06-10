<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\UserSearchHistory;
use App\Services\ProductNotificationService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource. Hiển thị danh sách sản phẩm
     */
    public function index()
    {
        $products = Product::with('user')
            ->where('is_approved', 1)
            ->where('updated_at', '>', now()->subDays(7)) // Chỉ lấy sản phẩm chưa hết hạn
            ->get();
        return view('products.index', ['products' => $products]);
    }


    public function approved()
    {
        $products = Product::with('user')
            ->where('is_approved', 0)
            ->get();

        return view('products.approved', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Nếu là Admin hoặc Super-Admin thì chặn
        if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super-Admin')) {
            abort(403, 'Admin không được phép đăng sản phẩm.');
        }

        $categories = Category::all();
        return view('products.create', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'deal_type' => 'required|in:price,exchange,free',
            'price' => 'required_if:deal_type,price|nullable|numeric|min:0',
            'description' => 'required',
            'images' => 'required|array|max:4',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'categories' => 'required_without:new_category|array',
            'categories.*' => 'exists:categories,id',
            'new_category' => 'nullable|required_without:categories|string|max:255',
            'payment_method' => 'required|string|max:255',
            'location_primary' => 'required|string|max:255',
            'location_secondary' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'used_duration' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:255',
        ]);

        // Gán user hiện tại
        $validated['user_id'] = auth()->id() ?? 1;

        // Gán deal_type
        $dealType = $request->input('deal_type');
        $validated['deal_type'] = $dealType;

        // Gán price theo loại giao dịch
        if ($dealType === 'price') {
            $validated['price'] = $request->input('price');
        } elseif ($dealType === 'free') {
            $validated['price'] = 0;
        } else {
            $validated['price'] = null;
        }

        // Kiểm tra xem có phải Super-Admin không
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('Super-Admin');

        // Xử lý ảnh với kiểm duyệt (cho user thường và Admin, ngoại trừ Super-Admin)
        $images = $request->file('images', []);
        $imageNames = [];
        
        if (!$isSuperAdmin) {
            // USER THƯỜNG hoặc ADMIN: Phải kiểm duyệt ảnh
            $tempPaths = [];
            $allModerationResults = [];
            $hasBlockedImage = false;
            $errorMessages = [];

            // Bước 1: Lưu tạm TẤT CẢ ảnh trước
            foreach ($images as $index => $image) {
                $tempFileName = 'temp_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $tempPath = $image->storeAs('temp', $tempFileName);
                $fullPath = storage_path('app/' . $tempPath);
                $tempPaths[] = $tempPath;

                if (!file_exists($fullPath)) {
                    // Xóa các file tạm nếu có lỗi
                    foreach ($tempPaths as $path) {
                        if (Storage::exists($path)) {
                            Storage::delete($path);
                        }
                    }
                    return back()->withErrors(['images' => "Ảnh thứ " . ($index + 1) . ": File không thể lưu tạm thời"])->withInput();
                }
            }

            // Bước 2: Kiểm duyệt TẤT CẢ ảnh
            $allDebugResults = [];
            foreach ($tempPaths as $index => $tempPath) {
                $result = $this->kiemDuyet($tempPath, $index + 1, $allDebugResults);
                $allModerationResults[] = [
                    'index' => $index + 1, 
                    'path' => $tempPath,
                    'result' => $result
                ];

                // Nếu có ảnh bị chặn, ghi nhận lại nhưng vẫn tiếp tục kiểm tra các ảnh khác
                if ($result['error']) {
                    $hasBlockedImage = true;
                    $errorMessages[] = "Ảnh thứ " . ($index + 1) . ": " . $result['message'];
                }
            }

            // Bước 3: Nếu có ảnh nào bị chặn, xóa tất cả file tạm và báo lỗi
            if ($hasBlockedImage) {
                // Xóa tất cả file tạm
                foreach ($tempPaths as $path) {
                    if (Storage::exists($path)) {
                        Storage::delete($path);
                    }
                }
                
                // Trả về với thông báo lỗi
                return redirect()->route('home')->with('error', 'Ảnh chứa nội dung không phù hợp: ' . implode(' | ', $errorMessages));
            }

            // Bước 4: Tất cả ảnh đã được duyệt, lưu vào thư mục chính
            foreach ($images as $image) {
                $name = $image->hashName();
                $image->move(public_path('images'), $name);
                $imageNames[] = $name;
            }

            // Bước 5: Xóa các file tạm
            foreach ($tempPaths as $path) {
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
        } else {
            // SUPER-ADMIN: Không cần kiểm duyệt, lưu trực tiếp
            foreach ($images as $image) {
                $name = $image->hashName();
                $image->move(public_path('images'), $name);
                $imageNames[] = $name;
            }
        }

        // Tạo sản phẩm mới
        // Trong phương thức store, thay đoạn tạo sản phẩm mới:
        $product = new Product($validated);
        $product->image = json_encode($imageNames);
        $product->is_approved = $isSuperAdmin ? 1 : 0;
        $product->save();

        // Gắn danh mục
        $categoryIds = $request->input('categories', []);

        if ($request->filled('new_category')) {
            $newCategory = \App\Models\Category::firstOrCreate([
                'name' => trim($request->new_category),
            ]);
            $categoryIds[] = $newCategory->id;
        }

        if (!empty($categoryIds)) {
            $product->categories()->attach($categoryIds);
        }

        // Gửi thông báo cho người theo dõi
        ProductNotificationService::notifyFollowersNewProduct($product);

        // Thông báo phù hợp
        if ($isSuperAdmin) {
            return redirect()->route('home')->with('success', 'Đăng sản phẩm thành công! (Đã duyệt) - Tất cả ' . count($images) . ' ảnh đã được kiểm duyệt.');
        } else {
            return redirect()->route('home')->with('success', 'Bài đăng của bạn đã được gửi và đang chờ kiểm duyệt! - Tất cả ' . count($images) . ' ảnh đã được kiểm duyệt.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with(['user', 'categories'])->findOrFail($id);

        $relatedProducts = collect();
        $category = $product->categories->first();

        if ($category) {
            $relatedProducts = $category->products()
                ->where('products.id', '!=', $product->id)
                ->where('products.is_approved', 1)
                ->where('products.updated_at', '>', now()->subDays(7))
                ->with(['user', 'categories'])
                ->latest()
                ->take(12)
                ->get();
        }

        $isInCart = auth()->check() && \App\Models\Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->exists();

        return view('products.show', compact('product', 'relatedProducts', 'isInCart'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);

        $isAdmin = auth()->check() && auth()->user()->hasAnyRole(['Admin', 'Super-Admin']);
        if (!$isAdmin && $product->user_id != auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa sản phẩm này.');
        }


        $categories = Category::all();
        return view('products.edit', compact('product', 'categories', 'isAdmin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        if ($product->user_id != auth()->id()) {
            abort(403, 'Bạn không có quyền cập nhật sản phẩm này.');
        }

        $dealType = $request->input('deal_type');
        $priceRule = $dealType === 'price' ? 'required|numeric|min:0' : 'nullable';

        $validated = $request->validate([
            'name' => 'required|max:255',
            'deal_type' => 'required|in:price,exchange,free',
            'price' => $priceRule,
            'description' => 'required',
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
            'new_category' => 'nullable|string|max:255',
            'new_images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'status' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'used_duration' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:255',
            'condition' => 'required|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'location_primary' => 'required|string|max:255',
            'location_secondary' => 'nullable|string|max:255',
            'existing_images' => 'sometimes|array',
            'existing_images.*' => 'string'
        ]);

        // Kiểm tra nếu không có categories và new_category
        if (empty($validated['categories']) && !$request->filled('new_category')) {
            return back()
                ->withErrors(['categories' => 'Vui lòng chọn hoặc nhập ít nhất một danh mục.'])
                ->withInput();
        }

        // Kiểm tra xem có phải Admin không
        $isAdmin = auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super-Admin'));

        // -------------------
        // Xử lý hình ảnh
        // -------------------
        $currentImages = json_decode($product->image, true) ?? [];
        // Nếu không gửi existing_images thì giữ nguyên ảnh cũ
        $existingImages = $request->input('existing_images', $currentImages);

        $removedImages = array_diff($currentImages, $existingImages);
        foreach ($removedImages as $img) {
            $imgPath = public_path('images/' . $img);
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
        }

        $newImageNames = [];
        
        if ($request->hasFile('new_images')) {
            $newImagesFiles = $request->file('new_images');
            
            if (!$isAdmin) {
                // USER THƯỜNG: Phải kiểm duyệt ảnh mới
                $tempPaths = [];
                $allModerationResults = [];
                $hasBlockedImage = false;
                $errorMessages = [];

                // Bước 1: Lưu tạm TẤT CẢ ảnh mới trước
                foreach ($newImagesFiles as $index => $image) {
                    $tempFileName = 'temp_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $tempPath = $image->storeAs('temp', $tempFileName);
                    $fullPath = storage_path('app/' . $tempPath);
                    $tempPaths[] = $tempPath;

                    if (!file_exists($fullPath)) {
                        // Xóa các file tạm nếu có lỗi
                        foreach ($tempPaths as $path) {
                            if (Storage::exists($path)) {
                                Storage::delete($path);
                            }
                        }
                        return back()->withErrors(['new_images' => "Ảnh mới thứ " . ($index + 1) . ": File không thể lưu tạm thời"])->withInput();
                    }
                }

                // Bước 2: Kiểm duyệt TẤT CẢ ảnh mới
                $allDebugResults = [];
                foreach ($tempPaths as $index => $tempPath) {
                    $result = $this->kiemDuyet($tempPath, $index + 1, $allDebugResults);
                    $allModerationResults[] = [
                        'index' => $index + 1, 
                        'path' => $tempPath,
                        'result' => $result
                    ];

                    // Nếu có ảnh bị chặn, ghi nhận lại nhưng vẫn tiếp tục kiểm tra các ảnh khác
                    if ($result['error']) {
                        $hasBlockedImage = true;
                        $errorMessages[] = "Ảnh mới thứ " . ($index + 1) . ": " . $result['message'];
                    }
                }

                // Bước 3: Nếu có ảnh nào bị chặn, xóa tất cả file tạm và báo lỗi
                if ($hasBlockedImage) {
                    foreach ($tempPaths as $path) {
                        if (Storage::exists($path)) {
                            Storage::delete($path);
                        }
                    }

                    return redirect()->route('home')->with('error', 'Ảnh chứa nội dung không phù hợp, không thể cập nhật sản phẩm.');
                }

                // Bước 4: Tất cả ảnh mới đã được duyệt, lưu vào thư mục chính
                foreach ($newImagesFiles as $file) {
                    $filename = $file->hashName();
                    $file->move(public_path('images'), $filename);
                    $newImageNames[] = $filename;
                }

                // Bước 5: Xóa các file tạm
                foreach ($tempPaths as $path) {
                    if (Storage::exists($path)) {
                        Storage::delete($path);
                    }
                }
            } else {
                // ADMIN: Không cần kiểm duyệt, lưu trực tiếp
                foreach ($newImagesFiles as $file) {
                    $filename = $file->hashName();
                    $file->move(public_path('images'), $filename);
                    $newImageNames[] = $filename;
                }
            }
        }

        $allImages = array_slice(array_merge($existingImages, $newImageNames), 0, 4);
        if (count($allImages) === 0) {
            return back()->withErrors(['image' => 'Bạn phải chọn ít nhất một hình ảnh.'])->withInput();
        }

        // Lưu lịch sử
        \App\Models\ProductHistory::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'action' => 'update',
            'old_data' => json_encode($product->only(['name', 'description', 'price'])),
        ]);

        $product->update([
            'name' => $validated['name'],
            'deal_type' => $validated['deal_type'],
            'price' => $dealType === 'price' ? $validated['price'] : null,
            'description' => $validated['description'],
            'image' => json_encode($allImages),
            'status' => $validated['status'] ?? null,
            'material' => $validated['material'] ?? null,
            'size' => $validated['size'] ?? null,
            'brand' => $validated['brand'] ?? null,
            'used_duration' => $validated['used_duration'] ?? null,
            'reason' => $validated['reason'] ?? null,
            'condition' => $validated['condition'],
            'payment_method' => $validated['payment_method'] ?? null,
            'location_primary' => $validated['location_primary'],
            'location_secondary' => $validated['location_secondary'] ?? null,
            'new_category' => $validated['new_category'] ?? null,
        ]);

        $product->categories()->sync($validated['categories'] ?? []);

        // Thông báo thành công với số lượng ảnh đã kiểm duyệt
        $totalImages = count($newImageNames);
        if ($totalImages > 0) {
            $message = $isAdmin 
                ? "Cập nhật thành công! (Đã duyệt) - {$totalImages} ảnh mới đã được kiểm duyệt."
                : "Cập nhật thành công! - {$totalImages} ảnh mới đã được kiểm duyệt.";
        } else {
            $message = 'Cập nhật thành công!';
        }
        return redirect()->route('home')->with('success', $message);
    }

    public function deleteImage(Request $request, Product $product)
    {
        $user = auth()->user();

        // Chỉ cho phép Admin hoặc chủ sở hữu sản phẩm xóa ảnh
        $isAdmin = $user->hasAnyRole(['Admin', 'Super-Admin']);
        $isOwner = $product->user_id === $user->id;

        if (!$isAdmin && !$isOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa ảnh sản phẩm này.'
            ], 403);
        }

        $imageToDelete = $request->input('image');

        if (!$imageToDelete) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu tên ảnh cần xóa.'
            ], 422);
        }

        // Lấy danh sách ảnh hiện tại
        $currentImages = is_array($product->image)
            ? $product->image
            : json_decode($product->image ?? '[]', true);

        if (!in_array($imageToDelete, $currentImages)) {
            return response()->json([
                'success' => false,
                'message' => 'Ảnh không tồn tại trong sản phẩm.'
            ], 404);
        }

        // Xóa ảnh vật lý nếu tồn tại
        $imagePath = public_path('images/' . $imageToDelete);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        // Cập nhật DB sau khi xoá
        $updatedImages = array_values(array_filter($currentImages, fn($img) => $img !== $imageToDelete));
        $product->image = json_encode($updatedImages);
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Xóa ảnh thành công.',
            'images' => $updatedImages
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $user = auth()->user();

        // Chỉ cho phép xóa nếu là chủ sở hữu
        if ($product->user_id !== $user->id) {
            abort(403, 'Bạn chỉ có thể xóa sản phẩm của chính mình.');
        }

        // Ghi nhận người xóa + xóa mềm
        $product->update(['deleted_by' => $user->id]);
        $product->delete();

        return redirect()->route('products.my')
            ->with('success', 'Sản phẩm đã được chuyển vào thùng rác.');
    }

    public function search(Request $request)
    {
        // Lưu lịch sử tìm kiếm
        $keyword = $request->q;
        if (!empty($keyword) && auth()->check()) {
            UserSearchHistory::where('user_id', auth()->id())
                ->where('keyword', $keyword)
                ->delete();
            UserSearchHistory::create([
                'user_id' => auth()->id(),
                'keyword' => $keyword,
            ]);
        }

        $query = Product::with(['user', 'categories'])
            ->where('is_approved', 1)
            ->where('updated_at', '>', now()->subDays(7));

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('seller')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->seller . '%');
            });
        }

        if ($request->filled('deal_type')) {
            $query->where('deal_type', $request->deal_type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('updated_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('updated_at', 'asc');
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12);
        $categories = \App\Models\Category::all();

        return view('products.search', compact('products', 'categories'));
    }

    public function userBin()
    {
        $products = Product::onlyTrashed()
            ->where('user_id', auth()->id())
            ->where('deleted_by', auth()->id())
            ->latest()
            ->paginate(12);

        return view('products.user-bin', [
            'products' => $products,
            'isAdminView' => false,
        ]);
    }

    public function restore(string $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        // Chỉ cho phép chủ sở hữu khôi phục
        if (auth()->id() !== $product->user_id) {
            abort(403, 'Bạn chỉ có thể khôi phục sản phẩm của chính mình.');
        }

        $product->restore();

        return back()->with('success', 'Đã khôi phục sản phẩm!');
    }

    public function forceDelete(string $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        // Chỉ cho phép chủ sở hữu xóa vĩnh viễn
        if (auth()->id() !== $product->user_id) {
            abort(403, 'Bạn chỉ có thể xóa vĩnh viễn sản phẩm của chính mình.');
        }

        // Xóa quan hệ danh mục trước
        $product->categories()->detach();

        // Xóa vĩnh viễn
        $product->forceDelete();

        return back()->with('success', 'Đã xóa vĩnh viễn sản phẩm!');
    }
    // Người dùng thường chỉ xóa sản phẩm của họ
    public function empty()
    {
        $userId = auth()->id();
        $deletedCount = 0;

        Product::onlyTrashed()
            ->where('user_id', $userId)
            ->each(function ($product) use (&$deletedCount) {
                $product->categories()->detach();
                $product->forceDelete();
                $deletedCount++;
            });

        return back()->with('success', "Đã dọn sạch $deletedCount sản phẩm khỏi thùng rác!");
    }

    public function getName(Request $request)
    {
        $products = Product::where('name', 'like', '%' . $request->q . '%')->where('is_approved', 1)
            ->get();
        return view('products.index', ['products' => $products]);
    }

    public function all(Request $request)
    {
        $query = Product::with(['user', 'categories'])
            ->where('is_approved', 1)
            ->where('updated_at', '>', now()->subDays(7));

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('seller')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->seller . '%');
            });
        }

        if ($request->filled('deal_type')) {
            $query->where('deal_type', $request->deal_type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('updated_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('updated_at', 'asc');
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12);
        $categories = \App\Models\Category::all();

        return view('products.all-product', compact('products', 'categories'));
    }

    public function myProducts()
    {
        $products = Product::where('user_id', auth()->id())
            ->with(['user', 'categories'])
            ->latest()
            ->paginate(10);

        return view('products.my', compact('products'));
    }

    public function reNew($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        if ($product->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền gia hạn sản phẩm này.');
        }

        $product->updated_at = now();
        if ($product->trashed()) {
            $product->restore();
        }
        $product->save();

        return redirect()->route('products.my')->with('success', 'Sản phẩm đã được gia hạn thêm 7 ngày.');
    }

    
    private function kiemDuyet($imagePath, $imageIndex = 1, &$allDebugResults = [])
    {
        $fullPath = storage_path('app/' . $imagePath);
        if (!file_exists($fullPath)) {
            return ['error' => true, 'message' => 'File ảnh không tồn tại: ' . $fullPath];
        }

        // Gọi API với models mới
        $response = Http::timeout(60)->attach(
            'media', fopen($fullPath, 'r'), basename($fullPath)
        )->post('https://api.sightengine.com/1.0/check.json', [
            'models' => 'nudity-2.1,weapon,alcohol,offensive-2.0,text-content,face-attributes,gore-2.0,qr-content,tobacco,violence,self-harm,money,gambling',
            'api_user' => '1653520810',
            'api_secret' => 'iHy84ZNriqQ7dt3j97YM4MBEP6xjAGiP',
        ]);

        if ($response->failed()) {
            return ['error' => false, 'method' => 'api_failed_allowed'];
        }

        $data = $response->json();

        if (isset($data['status']) && $data['status'] === 'failure') {
            return ['error' => false, 'method' => 'api_error_allowed'];
        }

        // Lấy các giá trị models
        $nudity = $data['nudity']['raw'] ?? 0;
        $alcohol = $data['alcohol']['prob'] ?? 0;
        $violence = $data['violence']['prob'] ?? 0;
        $gore = $data['gore']['prob'] ?? 0;
        $offensive = $data['offensive']['prob'] ?? 0;
        $tobacco = $data['tobacco']['prob'] ?? 0;
        $selfHarm = $data['self-harm']['prob'] ?? 0;
        $money = $data['money']['prob'] ?? 0;
        $gambling = $data['gambling']['prob'] ?? 0;

        // Các concept có thể trả về trong mảng concepts
        $concepts = $data['concepts'] ?? [];

        $weaponProb = 0;
        $textContentProb = 0;
        $qrContentProb = 0;
        $faceMinorProb = 0;

        foreach ($concepts as $concept) {
            switch ($concept['name']) {
                case 'weapon':
                    $weaponProb = $concept['prob'] ?? 0;
                    break;
                case 'text-content':
                    $textContentProb = $concept['prob'] ?? 0;
                    break;
                case 'qr':
                case 'qr-content':
                    $qrContentProb = $concept['prob'] ?? 0;
                    break;
                case 'face-minor':
                    $faceMinorProb = $concept['prob'] ?? 0;
                    break;
            }
        }

        // Kiểm tra ngưỡng
        $blockedReasons = [];

        if ($nudity > 0.7) $blockedReasons[] = 'nudity';
        if ($alcohol > 0.5) $blockedReasons[] = 'alcohol';
        if ($violence > 0.7) $blockedReasons[] = 'violence';
        if ($gore > 0.5) $blockedReasons[] = 'gore';
        if ($offensive > 0.7) $blockedReasons[] = 'offensive content';
        if ($tobacco > 0.5) $blockedReasons[] = 'tobacco';
        if ($selfHarm > 0.5) $blockedReasons[] = 'self-harm';
        if ($money > 0.5) $blockedReasons[] = 'money-related content';
        if ($gambling > 0.5) $blockedReasons[] = 'gambling';

        if ($weaponProb > 0.7) $blockedReasons[] = 'weapon';
        if ($textContentProb > 0.7) $blockedReasons[] = 'text content';
        if ($qrContentProb > 0.8) $blockedReasons[] = 'QR code';
        if ($faceMinorProb > 0.8) $blockedReasons[] = 'minor face detected';

        // Lưu debug
        $allDebugResults["Ảnh $imageIndex - " . basename($fullPath)] = [
            'MODELS SCORES' => [
                'nudity (raw)' => "$nudity (ngưỡng: 0.7)",
                'alcohol' => "$alcohol (ngưỡng: 0.5)",
                'violence' => "$violence (ngưỡng: 0.7)",
                'gore' => "$gore (ngưỡng: 0.5)",
                'offensive' => "$offensive (ngưỡng: 0.7)",
                'tobacco' => "$tobacco (ngưỡng: 0.5)",
                'self-harm' => "$selfHarm (ngưỡng: 0.5)",
                'money' => "$money (ngưỡng: 0.5)",
                'gambling' => "$gambling (ngưỡng: 0.5)"
            ],
            'CONCEPTS SCORES' => [
                'weapon' => "$weaponProb (ngưỡng: 0.7)",
                'text-content' => "$textContentProb (ngưỡng: 0.7)",
                'qr-content' => "$qrContentProb (ngưỡng: 0.8)",
                'face-minor' => "$faceMinorProb (ngưỡng: 0.8)"
            ],
            'KẾT QUẢ' => !empty($blockedReasons) ? 'BỊ CHẶN' : 'ĐƯỢC DUYỆT',
            'LÝ DO CHẶN' => $blockedReasons
        ];

        if (!empty($blockedReasons)) {
            return [
                'error' => true,
                'message' => 'Ảnh chứa nội dung không phù hợp: ' . implode(', ', $blockedReasons),
                'method' => 'api_blocked',
                'blocked_reasons' => $blockedReasons,
                'all_scores' => compact(
                    'nudity', 'alcohol', 'violence', 'gore', 'offensive',
                    'tobacco', 'selfHarm', 'money', 'gambling',
                    'weaponProb', 'textContentProb', 'qrContentProb', 'faceMinorProb'
                )
            ];
        }

        return ['error' => false, 'method' => 'api_approved'];
    }


public function loadMore(\Illuminate\Http\Request $request)
{
    $type    = $request->get('type', 'newest');  // newest | free | favorited
    $page    = (int) $request->get('page', 1);
    $perPage = (int) $request->get('per_page', 4);

    // Tùy cách bạn định nghĩa scope/điều kiện "đã duyệt"
    $base = \App\Models\Product::query()->with('user')
        ->where('is_approved', true);

    switch ($type) {
        case 'newest':
            $query = (clone $base)->latest('created_at');
            break;

        case 'free':
            $query = (clone $base)->where('deal_type', 'free')->where('price', 0)->latest('created_at');
            break;

        case 'favorited':
            // giả sử có quan hệ favoriters() ->withCount('favoriters')
            $query = (clone $base)->withCount('favoriters')->orderByDesc('favoriters_count')->latest('created_at');
            break;

        default:
            return response()->json(['message' => 'Loại danh sách không hợp lệ'], 400);
    }

    $products = $query->paginate($perPage, ['*'], 'page', $page);

    // Trả về HTML của <li> để gắn thẳng vào <ul>
    $html = view('components.product-cards-chunk', [
        'products' => $products
    ])->render();

    return response()->json([
        'html'      => $html,
        'has_more'  => $products->hasMorePages(),
        'next_page' => $products->currentPage() + 1,
    ]);
}


}
