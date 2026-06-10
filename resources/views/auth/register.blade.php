<x-guest-layout>
    <style>
        .register-card {
            background: white;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-width: 500px;
            width: 100%;
            margin: auto;
        }

        .register-card h2 {
            font-size: 24px;
            font-weight: 700;
            color: #4f46e5;
            text-align: center;
            margin-bottom: 24px;
        }

        .register-card input {
            border-radius: 8px;
            padding: 12px;
        }

        .register-card button {
            background: #4f46e5;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: background 0.2s ease;
        }

        .register-card button:hover {
            background: #4338ca;
        }
    </style>

    <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
        <div class="register-card">
            <h2>Đăng ký tài khoản</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <x-input-label for="name" :value="__('Họ và tên')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <x-input-label for="phone" :value="__('Số điện thoại')" />
                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required autocomplete="tel" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Mật khẩu')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-500" />
                </div>

                <div class="flex items-center justify-between">
                    <a class="text-sm text-indigo-600 hover:underline" href="{{ route('login') }}">
                        {{ __('Đã có tài khoản? Đăng nhập') }}
                    </a>

                    <button type="submit">
                        {{ __('Đăng ký') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
