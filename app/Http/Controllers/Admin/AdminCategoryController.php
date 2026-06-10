<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
        ], [
            'name.required' => 'Phải nhập tên danh mục',
            'name.max' => 'Tên danh mục quá dài',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục mới.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroy(Category $category)
    {
        $products = $category->products;

        foreach ($products as $product) {
            $categoryCount = $product->categories()->count();
            if ($categoryCount === 1) {
                $product->delete();
            } else {
                $product->categories()->detach($category->id);
            }
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục và xử lý sản phẩm liên quan.');
    }
}
