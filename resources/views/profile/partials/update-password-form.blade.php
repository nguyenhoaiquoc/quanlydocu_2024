<section class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6">
    <header class="mb-6">
        <h2 class="text-xl font-bold text-indigo-600">
            {{ __('Đổi mật khẩu') }}
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            {{ __('Hãy sử dụng một mật khẩu mạnh, dài và khó đoán để bảo vệ tài khoản của bạn.') }}
        </p>
    </header>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <!-- Mật khẩu hiện tại -->
        <div>
            <x-input-label for="update_password_current_password" :value="__('Mật khẩu hiện tại')" />
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full rounded-md"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Mật khẩu mới -->
        <div>
            <x-input-label for="update_password_password" :value="__('Mật khẩu mới')" />
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-1 block w-full rounded-md"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Nhập lại mật khẩu -->
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Xác nhận mật khẩu mới')" />
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full rounded-md"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Nút lưu -->
        <div class="flex items-center justify-between pt-4">
            <x-primary-button class="px-5 py-2">
                {{ __('Lưu thay đổi') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-600"
                >
                    {{ __('Đã lưu.') }}
                </p>
            @endif
        </div>
    </form>
</section>
