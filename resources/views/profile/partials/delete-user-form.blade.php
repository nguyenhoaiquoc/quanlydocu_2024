<section class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6">
    <header class="mb-6">
        <h2 class="text-xl font-bold text-red-600">
            {{ __('Xoá tài khoản') }}
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            {{ __('Khi xoá tài khoản, toàn bộ dữ liệu liên quan sẽ bị xoá vĩnh viễn. Vui lòng tải về mọi dữ liệu bạn muốn giữ lại trước khi tiếp tục.') }}
        </p>
    </header>

    <div class="text-right">
        <x-danger-button
            x-data
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            {{ __('Xoá tài khoản') }}
        </x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('profile.destroy') }}" class="bg-white p-6 rounded-lg">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-gray-900 mb-2">
                {{ __('Bạn chắc chắn muốn xoá tài khoản?') }}
            </h2>

            <p class="text-sm text-gray-600 mb-4">
                {{ __('Khi tài khoản bị xoá, toàn bộ dữ liệu sẽ không thể khôi phục. Nhập mật khẩu để xác nhận hành động này.') }}
            </p>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" value="{{ __('Mật khẩu') }}" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="{{ __('Nhập mật khẩu') }}"
                    class="block w-full rounded-md"
                    required
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-sm text-red-500" />
            </div>

            <div class="flex justify-end space-x-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ bỏ') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('Xoá vĩnh viễn') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
