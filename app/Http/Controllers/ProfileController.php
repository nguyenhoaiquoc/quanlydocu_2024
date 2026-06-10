<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Hiển thị form sửa thông tin cá nhân.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Xử lý cập nhật thông tin cá nhân.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Xử lý upload ảnh đại diện nếu có file mới
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = $file->hashName(); // Tạo tên file duy nhất
            $file->move(public_path('images'), $imageName);

            // Xóa ảnh cũ nếu có và tồn tại
            if ($user->image && file_exists(public_path('images/' . $user->image))) {
                @unlink(public_path('images/' . $user->image));
            }

            $data['image'] = $imageName;
        } else {
            // Nếu không upload file mới, loại bỏ field image
            unset($data['image']);
        }

        // Nếu đổi email thì reset xác minh email
        if (isset($data['email']) && $user->email !== $data['email']) {
            $data['email_verified_at'] = null;
        }

        // Gán giá trị bio nếu có trong request
        if ($request->has('bio')) {
            $data['bio'] = $request->input('bio');
        }

        // Cập nhật dữ liệu
        $user->fill($data);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Xóa tài khoản cá nhân.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
