<x-admin-layout>
    <div class="min-vh-100 d-flex justify-content-center align-items-center bg-light px-3">
        <div class="bg-white p-5 shadow rounded-4" style="max-width: 500px; width: 100%;">
            <h3 class="mb-4 text-primary fw-bold text-center">
                <i class="bi bi-pencil-fill me-2"></i> Chỉnh sửa danh mục
            </h3>

            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label fw-medium">Tên danh mục</label>
                    <input type="text" id="name" name="name"
                        value="{{ old('name', $category->name) }}"
                        class="form-control @error('name') is-invalid @enderror"
                        required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save me-1"></i> Lưu thay đổi
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ms-2">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
