<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => [
                'required',
                'string',
                'max:50',
                'unique:' . User::class,
                'regex:/^[a-zA-Z0-9._%+-]+@fit\.tdc\.edu\.vn$/'
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'regex:/^0\d{9}$/'],
        ], [
            'name.required' => 'Phải nhập tên',
            'name.max' => 'Tên quá dài',
            'email.regex' => 'Email phải là @fit.tdc.edu.vn',
            'phone.regex' => 'Số điện thoại phải gồm đúng 10 chữ số và bắt đầu bằng số 0',
            'password.required' => 'Phải nhập mật khẩu'
        ]);

        // Tạo mã xác minh
        $verificationCode = rand(100000, 999999);

        // Lưu thông tin vào session (tạm thời)
        $request->session()->put('registration_data', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'verification_code' => $verificationCode,
        ]);

        // Gửi mã qua email
        Mail::raw("Mã xác nhận của bạn là: $verificationCode", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Xác nhận đăng ký');
        });

        // Chuyển hướng đến trang nhập mã xác minh
        return redirect()->route('auth.verify-code');
    }

    public function showVerifyForm(): View
    {
        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $data = $request->session()->get('registration_data');

        if (!$data || $request->code != $data['verification_code']) {
            return back()->withErrors(['code' => 'Mã xác nhận không đúng.']);
        }


        // Đăng ký người dùng
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'is_verified' => true,
        ]);
        
        $user->assignRole('User');

        Auth::login($user);
        $request->session()->forget('registration_data');

        return redirect(RouteServiceProvider::HOME);
    }
}
