<x-guest-layout>
    <style>
        .reset-card {
            background: white;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-width: 480px;
            width: 100%;
            margin: auto;
        }

        .reset-card h2 {
            font-size: 24px;
            font-weight: 700;
            color: #4f46e5;
            text-align: center;
            margin-bottom: 24px;
        }

        .reset-card input {
            border-radius: 8px;
            padding: 12px;
        }

        .reset-card button {
            background: #4f46e5;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: background 0.2s ease;
        }

        .reset-card button:hover {
            background: #4338ca;
        }
    </style>

    <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
        <div class="reset-card">
            <h2>Đặt lại mật khẩu</h2>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Mật khẩu mới')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-500" />
                </div>

                <div class="d-grid">
                    <button type="submit" class="w-full">
                        {{ __('Đặt lại mật khẩu') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
