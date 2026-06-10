<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminUserController extends Controller
{
    /**
     * Hiển thị danh sách tất cả người dùng
     */
    public function index()
    {
        // Lấy danh sách người dùng, kèm theo số lượng sản phẩm đã đăng
        $users = User::withCount('products')->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã bị xóa.');
    }

    public function show($id)
    {
        $user = User::with(['products.favorites', 'comments', 'followers', 'followings'])->findOrFail($id);

        $favoriteCount = $user->products->sum(function ($product) {
            return $product->favorites->count();
        });

        return view('admin.users.show', compact('user', 'favoriteCount'));
    }


    public function toggleRole($id)
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('Admin')) {
            $user->removeRole('Admin');
            $user->assignRole('User');
        } else {
            $user->assignRole('Admin');
        }

        return back()->with('success', 'Đã chuyển vai trò người dùng.');
    }
    public function toggleHonor(User $user)
    {
        $user->is_honored = !$user->is_honored;
        $user->save();

        return back()->with('success', 'Cập nhật trạng thái vinh danh thành công.');
    }

    public function toggleTrust(User $user)
    {
        $user->is_trusted = !$user->is_trusted;
        $user->save();

        return back()->with('success', 'Cập nhật trạng thái đáng tin cậy thành công.');
    }
}
