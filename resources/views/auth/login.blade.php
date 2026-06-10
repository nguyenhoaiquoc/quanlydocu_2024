<x-guest-layout>
    <style>
        body {
            background: #f3f4f6;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
            margin: auto;
        }
        .login-card h2 {
            font-size: 24px;
            color: #4f46e5;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
        }
        .login-card input {
            border-radius: 8px;
            padding: 12px;
        }
        .login-card button {
            background: #4f46e5;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: background 0.2s ease;
        }
        .login-card button:hover {
            background: #4338ca;
        }
        .login-card a {
            color: #4f46e5;
        }
        .login-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px auto;
            display: block;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>

    <div class="flex items-center justify-center min-h-screen">
        <div class="login-card">
            <!-- Logo -->
            <img src="{{ asset('images/logo-fittdc.png') }}" alt="Logo" class="login-logo">

            <h2>Đăng nhập vào tài khoản</h2>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Remember Me & Forgot -->
                <div class="flex items-center justify-between mb-4 text-sm text-gray-600">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2">Ghi nhớ đăng nhập</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="hover:underline">Quên mật khẩu?</a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="mb-4">
                    <button type="submit" class="w-full">
                        ĐĂNG NHẬP
                    </button>
                </div>
            </form>

            <!-- Register -->
            <div class="text-center text-sm">
                Chưa có tài khoản?
                <a href="{{ route('register') }}" class="font-semibold hover:underline">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</x-guest-layout>
