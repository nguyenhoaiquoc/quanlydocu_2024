<section 
    x-data="{
        imgPreview: null,
        onFileChange(e){
            const [file] = e.target.files || [];
            if (file) this.imgPreview = URL.createObjectURL(file);
        }
    }"
    class="max-w-5xl mx-auto">

    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Hồ sơ cá nhân</h2>
        <p class="mt-2 text-sm text-gray-600">Cập nhật thông tin hiển thị, liên hệ và ảnh đại diện của bạn.</p>
    </div>

    <!-- Khung chính -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Sidebar: Avatar + Trạng thái -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <img
                            x-show="!imgPreview"
                            src="{{ $user->image ? asset('images/' . $user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=160' }}"
                            alt="avatar"
                            class="w-24 h-24 rounded-full object-cover ring-2 ring-indigo-100">
                        <img
                            x-show="imgPreview"
                            :src="imgPreview"
                            alt="preview"
                            class="w-24 h-24 rounded-full object-cover ring-2 ring-indigo-200">
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ảnh đại diện</p>
                        <p class="text-xs text-gray-400">PNG, JPG tối đa ~2MB</p>
                        <label for="image" class="inline-flex mt-2 items-center px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 cursor-pointer">
                            Chọn ảnh
                        </label>
                        <input id="image" name="image" type="file" accept="image/*" class="hidden" form="profile-form" @change="onFileChange">
                        <x-input-error class="mt-2 text-sm text-red-500" :messages="$errors->get('image')" />
                    </div>
                </div>

                <div class="mt-6 border-t pt-6">
                    <p class="text-sm font-medium text-gray-700">Trạng thái tài khoản</p>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                        <div class="mt-3 rounded-lg border border-yellow-200 bg-yellow-50 p-3">
                            <p class="text-sm text-yellow-800">
                                Email của bạn <span class="font-semibold">{{ $user->email }}</span> chưa được xác minh.
                            </p>
                            <div class="mt-2">
                                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button class="text-sm font-medium text-indigo-600 hover:text-indigo-700 underline">
                                        Gửi lại email xác minh
                                    </button>
                                </form>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="text-xs text-green-600 mt-2">Liên kết xác minh mới đã được gửi.</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="mt-3 flex items-center gap-2 text-sm text-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            Email đã xác minh
                        </div>
                    @endif
                </div>
            </div>
        </aside>

        <!-- Form chính -->
        <div class="lg:col-span-2">
            <form id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('patch')

                <!-- Khối: Thông tin cơ bản -->
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6">
                    <h3 class="text-base font-semibold text-gray-900">Thông tin cơ bản</h3>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="name" :value="__('Tên hiển thị')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2 text-sm text-red-500" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2 text-sm text-red-500" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="__('Số điện thoại')" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
                            <x-input-error class="mt-2 text-sm text-red-500" :messages="$errors->get('phone')" />
                        </div>

                        <div>
                            <x-input-label for="gender" :value="__('Giới tính')" />
                            <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="" disabled {{ old('gender', $user->gender) ? '' : 'selected' }}>Chọn giới tính</option>
                                <option value="Nam" {{ old('gender', $user->gender) == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gender', $user->gender) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('gender', $user->gender) == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                            <x-input-error class="mt-2 text-sm text-red-500" :messages="$errors->get('gender')" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="address" :value="__('Địa chỉ')" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" autocomplete="street-address" />
                            <x-input-error class="mt-2 text-sm text-red-500" :messages="$errors->get('address')" />
                        </div>
                    </div>
                </div>

                <!-- Khối: Giới thiệu -->
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6">
                    <h3 class="text-base font-semibold text-gray-900">Giới thiệu bản thân</h3>
                    <div class="mt-4">
                        <textarea id="bio" name="bio" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-300" placeholder="Viết vài dòng về bạn...">{{ old('bio', $user->bio) }}</textarea>
                        <x-input-error class="mt-2 text-sm text-red-500" :messages="$errors->get('bio')" />
                        <div class="mt-2 flex justify-between text-xs text-gray-400">
                            <span>Gợi ý: nêu ngắn gọn chuyên môn, sở thích, mục tiêu.</span>
                            <span>{{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <x-primary-button class="px-6 py-2">Lưu thay đổi</x-primary-button>

                    @if (session('status') === 'profile-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-600">
                            Đã lưu.
                        </p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>
