<x-guest-layout>
    <style>
        .verify-email-card {
            background: white;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-width: 480px;
            width: 100%;
            margin: auto;
        }

        .verify-email-card h2 {
            font-size: 22px;
            font-weight: 700;
            color: #4f46e5;
            text-align: center;
            margin-bottom: 20px;
        }

        .verify-email-card p {
            font-size: 14px;
            color: #555;
            text-align: center;
            margin-bottom: 24px;
        }

        .verify-email-card button {
            background: #4f46e5;
            color: white;
            font-weight: 600;
            padding: 10px 16px;
            border-radius: 8px;
            transition: background 0.2s ease;
        }

        .verify-email-card button:hover {
            background: #4338ca;
        }

        .verify-email-card .text-green-600 {
            text-align: center;
            margin-bottom: 16px;
        }

        .verify-email-card form {
            display: inline-block;
        }
    </style>

    <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
        <div class="verify-email-card">
            <h2>Xác minh địa chỉ email</h2>

            <p>
                {{ __('Cảm ơn bạn đã đăng ký! Vui lòng kiểm tra email và nhấn vào liên kết xác minh. Nếu bạn chưa nhận được email, chúng tôi sẽ gửi lại.') }}
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="text-green-600 font-medium text-sm">
                    {{ __('Liên kết xác minh mới đã được gửi đến địa chỉ email của bạn.') }}
                </div>
            @endif

            <div class="flex justify-between items-center mt-4 gap-4 flex-wrap sm:flex-nowrap">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit">
                        {{ __('Gửi lại email xác minh') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm underline text-gray-600 hover:text-gray-900 focus:outline-none">
                        {{ __('Đăng xuất') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
