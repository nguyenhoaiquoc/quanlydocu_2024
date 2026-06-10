<x-guest-layout>
    <style>
        .confirm-card {
            background: white;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-width: 460px;
            width: 100%;
            margin: auto;
        }

        .confirm-card h2 {
            font-size: 22px;
            font-weight: 700;
            color: #4f46e5;
            text-align: center;
            margin-bottom: 20px;
        }

        .confirm-card p {
            font-size: 14px;
            color: #555;
            text-align: center;
            margin-bottom: 24px;
        }

        .confirm-card input {
            border-radius: 8px;
            padding: 12px;
        }

        .confirm-card button {
            background: #4f46e5;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: background 0.2s ease;
        }

        .confirm-card button:hover {
            background: #4338ca;
        }
    </style>

    <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
        <div class="confirm-card">
            <h2>Xác nhận mật khẩu</h2>

            <p>
                {{ __('Đây là khu vực bảo mật. Vui lòng nhập lại mật khẩu để tiếp tục.') }}
            </p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <!-- Password -->
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Mật khẩu')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
                </div>

                <div class="mt-4 d-grid">
                    <button type="submit" class="w-full">
                        {{ __('Xác nhận') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
