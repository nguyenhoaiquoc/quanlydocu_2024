<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductNotification;
use App\Services\ProductNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['user', 'categories'])->where('is_approved', 1);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->get();

        return view('admin.products.index', compact('products'));
    }

    /**
     * Hiển thị các sản phẩm đang chờ duyệt
     */
    public function approved(Request $request)
    {
        $query = Product::with('user', 'categories')
            ->where('is_approved', 0);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(10);

        return view('admin.products.approved', compact('products'));
    }

    /**
     * Toggle duyệt sản phẩm
     */
    public function toggleApprove($id)
    {
        $product = Product::findOrFail($id);
        $product->is_approved = !$product->is_approved;
        $product->save();

        // Nếu vừa được duyệt (từ 0 -> 1)
        if ($product->is_approved) {
            ProductNotificationService::notifyProductApproved($product, auth()->user());
        }

        $message = $product->is_approved
            ? 'Sản phẩm đã được duyệt!'
            : 'Sản phẩm đã chuyển về chờ duyệt!';

        return back()->with('success', $message);
    }

    /**
     * Hiển thị danh sách thùng rác cho Admin
     */
    public function bin()
    {
        $products = Product::onlyTrashed()
            ->with('user', 'categories')
            ->latest()
            ->paginate(12);
        return view('admin.products.admin-bin', compact('products'));
    }

    /**
     * Khôi phục sản phẩm đã xóa
     */
    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return back()->with('success', 'Sản phẩm đã được khôi phục.');
    }

    /**
     * Xóa vĩnh viễn sản phẩm
     */
    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->categories()->detach();
        $product->forceDelete();

        return back()->with('success', 'Sản phẩm đã bị xóa vĩnh viễn.');
    }

    /**
     * Dọn sạch thùng rác Admin
     */
    public function emptyBin()
    {
        $deletedCount = 0;
        Product::onlyTrashed()->each(function ($product) use (&$deletedCount) {
            $product->categories()->detach();
            $product->forceDelete();
            $deletedCount++;
        });

        return back()->with('success', "Đã dọn sạch $deletedCount sản phẩm khỏi thùng rác.");
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm (Admin quyền chỉnh mọi sản phẩm)
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $isAdmin = true;

        return view('products.edit', compact('product', 'categories', 'isAdmin'));
    }

    /**
     * Cập nhật sản phẩm (Admin)
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $dealType = $request->input('deal_type');
        $priceRule = $dealType === 'price' ? 'required|numeric|min:0' : 'nullable';

        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'deal_type' => 'required|in:price,exchange,free',
            'price' => $priceRule,
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'new_category' => 'nullable|string|max:255',
            'new_images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'existing_images' => 'array',
            'existing_images.*' => 'string',
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
        ]);

        /* ---------------- ẢNH ---------------- */
        $currentImages = json_decode($product->image, true) ?? [];
        $existingImages = $request->input('existing_images', []);
        $removedImages = array_diff($currentImages, $existingImages);

        foreach ($removedImages as $img) {
            $imgPath = public_path('images/' . $img);
            if (file_exists($imgPath)) {
                @unlink($imgPath);
            }
        }

        $newImageNames = [];
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $file) {
                $filename = $file->hashName();
                $file->move(public_path('images'), $filename);
                $newImageNames[] = $filename;
            }
        }

        $allImages = array_slice(array_merge($existingImages, $newImageNames), 0, 4);

        if (count($allImages) === 0) {
            return back()
                ->withErrors(['images_required' => 'Sản phẩm phải có ít nhất một hình ảnh.'])
                ->withInput();
        }

        /* ---------------- DANH MỤC ---------------- */
        $categoryIds = $validated['categories'] ?? [];

        if (empty($categoryIds) && !$request->filled('new_category')) {
            return back()
                ->withErrors(['categories' => 'Vui lòng chọn ít nhất một danh mục hoặc nhập danh mục ghi chú.'])
                ->withInput();
        }

        $newCategoryText = $request->filled('new_category')
            ? trim($request->new_category)
            : null;

        if (empty($categoryIds)) {
            $syncIds = [];
        } else {
            $syncIds = $categoryIds;
        }

        /* ---------------- CẬP NHẬT SẢN PHẨM ---------------- */
        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'deal_type' => $validated['deal_type'],
            'price' => $dealType === 'price' ? $validated['price'] : null,
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
            'new_category' => $newCategoryText,
        ]);

        $product->categories()->sync($syncIds);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Cập nhật sản phẩm thành công! (Admin)');
    }

    /**
     * Xóa mềm sản phẩm (Admin)
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $user = auth()->user();

        $product->update(['deleted_by' => $user->id]);
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được chuyển vào thùng rác (Admin).');
    }

    public function byCategory()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.products.by-category', compact('categories'));
    }

    public function byDealType()
    {
        $products = Product::select('deal_type', DB::raw('count(*) as total'))
            ->groupBy('deal_type')
            ->get();
        return view('admin.products.by-deal-type', compact('products'));
    }
}