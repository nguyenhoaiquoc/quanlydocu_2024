<x-admin-layout>
    <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light px-3">
        <div class="bg-white shadow rounded-4 w-100" style="max-width: 520px; padding: 40px 30px;">
            <h2 class="text-center mb-4 fw-bold text-primary d-flex justify-content-center align-items-center gap-2">
                📁 Tạo danh mục mới
            </h2>

            <form action="{{ route('categories.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-medium">Tên danh mục</label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Nhập tên danh mục..."
                           value="{{ old('name') }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                        <i class="fas fa-plus me-2"></i> Thêm danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
