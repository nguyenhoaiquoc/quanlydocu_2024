<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $search = $request->input('q');
        $sellerName = $request->input('seller');

        $products = $category->products()
            ->where('is_approved', 1)
            ->when(
                $search,
                fn($q) =>
                $q->where('name', 'like', '%' . $search . '%')
            )
            ->when(
                $minPrice,
                fn($q) =>
                $q->where('price', '>=', $minPrice)
            )
            ->when(
                $maxPrice,
                fn($q) =>
                $q->where('price', '<=', $maxPrice)
            )
            ->when(
                $sellerName,
                fn($q) =>
                $q->whereHas('user', function ($query) use ($sellerName) {
                    $query->where('name', 'like', '%' . $sellerName . '%');
                })
            )
            ->get();

        $category->setRelation('products', $products);

        return view('categories.show', compact('category'));
    }
}
