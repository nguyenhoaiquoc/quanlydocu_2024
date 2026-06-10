<x-guest-layout>
    <style>
        .forgot-card {
            background: white;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-width: 420px;
            width: 100%;
            margin: auto;
        }
        .forgot-card h2 {
            font-size: 22px;
            font-weight: 700;
            color: #4f46e5;
            text-align: center;
            margin-bottom: 20px;
        }
        .forgot-card p {
            font-size: 14px;
            color: #555;
            text-align: center;
            margin-bottom: 24px;
        }
        .forgot-card input {
            border-radius: 8px;
            padding: 12px;
        }
        .forgot-card button {
            background: #4f46e5;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: background 0.2s ease;
        }
        .forgot-card button:hover {
            background: #4338ca;
        }
    </style>

    <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
        <div class="forgot-card">
            <h2>Quên mật khẩu</h2>

            <p>{{ __('Không sao cả, hãy nhập email và chúng tôi sẽ gửi cho bạn liên kết đặt lại mật khẩu.') }}</p>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500" />
                </div>

                <div class="mt-4">
                    <button type="submit" class="w-full">
                        {{ __('Gửi liên kết đặt lại mật khẩu') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
