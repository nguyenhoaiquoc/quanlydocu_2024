<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        $exists = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if (!$exists) {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function index()
    {
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();

        return view('cart.index', compact('cartItems'));
    }

    public function remove($id)
    {
        $cart = Cart::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $cart->delete();

        return redirect()->route('cart.index')->with('success', 'Đã xóa khỏi giỏ hàng');
    }
}
