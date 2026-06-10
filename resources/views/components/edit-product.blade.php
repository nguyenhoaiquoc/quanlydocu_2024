<div class="container pt-5 mt-5">
    <h2 class="mb-4 text-center">✏️ Chỉnh sửa sản phẩm</h2>

    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST"
        action="{{ $isAdmin ? route('admin.products.update', $product->id) : route('products.update', $product->id) }}"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- DANH MỤC --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Danh mục sản phẩm <span class="text-danger">*</span></label><br>
            @foreach ($categories as $category)
                <input type="checkbox" class="btn-check" id="cat-{{ $category->id }}" name="categories[]"
                    value="{{ $category->id }}" {{ $product->categories->contains($category->id) ? 'checked' : '' }}>
                <label class="btn btn-outline-primary mb-1 me-1" for="cat-{{ $category->id }}">{{ $category->name }}</label>
            @endforeach
            @error('categories') <div class="text-danger mt-1">{{ $message }}</div> @enderror

            {{-- Cho phép nhập danh mục mới --}}
            <div class="mt-2">
                <input type="text" name="new_category" class="form-control" placeholder="Hoặc nhập danh mục mới..."
                    value="{{ old('new_category', $product->new_category) }}">
                <div class="form-text">Bạn có thể tự thêm một danh mục nếu chưa có trong danh sách.</div>
            </div>
        </div>


        {{-- TIÊU ĐỀ --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tiêu đề <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" maxlength="255" required
                value="{{ old('name', $product->name) }}">
        </div>

        {{-- MÔ TẢ --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Mô tả chi tiết <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" rows="5" maxlength="15000"
                required>{{ old('description', $product->description) }}</textarea>
        </div>

        {{-- THÔNG TIN CHI TIẺT --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tình trạng</label>
                <select name="condition" class="form-select" required>
                    <option value="">-- Chọn tình trạng --</option>
                    @php
                        $selectedCondition = old('condition', $product->condition);
                    @endphp
                    <option value="Mới 100%" {{ $selectedCondition === 'Mới 100%' ? 'selected' : '' }}>Mới 100%</option>
                    <option value="Như mới" {{ $selectedCondition === 'Như mới' ? 'selected' : '' }}>Như mới</option>
                    <option value="Đã qua sử dụng" {{ $selectedCondition === 'Đã qua sử dụng' ? 'selected' : '' }}>Đã qua
                        sử dụng</option>
                    <option value="Cũ" {{ $selectedCondition === 'Cũ' ? 'selected' : '' }}>Cũ</option>
                </select>

            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Chất liệu</label>
                <input type="text" name="material" class="form-control"
                    value="{{ old('material', $product->material) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Kích thước</label>
                <input type="text" name="size" class="form-control" value="{{ old('size', $product->size) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Thương hiệu</label>
                <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Thời gian sử dụng</label>
                <input type="text" name="used_duration" class="form-control"
                    value="{{ old('used_duration', $product->used_duration) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Lý do bán</label>
                <input type="text" name="reason" class="form-control" value="{{ old('reason', $product->reason) }}">
            </div>
        </div>

        {{-- GIÁ --}}
        <div class="mb-3" id="price-input-group">
            <label class="form-label fw-bold">Giá bán mong muốn (VNĐ) <span class="text-danger">*</span></label>
            <input type="number" name="price" class="form-control" min="0" value="{{ old('price', $product->price) }}">
        </div>

        {{-- HÌNH THỨC GIAO DỊCH --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Hình thức giao dịch <span class="text-danger">*</span></label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="deal_type" value="price" id="deal_price" {{ $product->deal_type === 'price' ? 'checked' : '' }}>
                <label class="form-check-label" for="deal_price">Bán với giá mong muốn</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="deal_type" value="exchange" id="deal_exchange" {{ $product->deal_type === 'exchange' ? 'checked' : '' }}>
                <label class="form-check-label" for="deal_exchange">Trao đổi</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="deal_type" value="free" id="deal_free" {{ $product->deal_type === 'free' ? 'checked' : '' }}>
                <label class="form-check-label" for="deal_free">Miễn phí</label>
            </div>
        </div>

        {{-- THANH TOÁN --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Hình thức thanh toán</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="payment_cash"
                    value="Thanh toán bằng tiền mặt" {{ $product->payment_method === 'Thanh toán bằng tiền mặt' ? 'checked' : '' }}>
                <label class="form-check-label" for="payment_cash">Tiền mặt</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="payment_transfer"
                    value="Thanh toán bằng chuyển khoản trực tuyến" {{ $product->payment_method === 'Thanh toán bằng chuyển khoản trực tuyến' ? 'checked' : '' }}>
                <label class="form-check-label" for="payment_transfer">Chuyển khoản trực tuyến</label>
            </div>
        </div>

        {{-- ĐỊA ĐIỂM --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Nơi giao dịch chính <span class="text-danger">*</span></label>
            <input type="text" name="location_primary" class="form-control" required
                value="{{ old('location_primary', $product->location_primary) }}">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Nơi giao dịch khác (tuùy chọn)</label>
            <input type="text" name="location_secondary" class="form-control"
                value="{{ old('location_secondary', $product->location_secondary) }}">
        </div>

        {{-- HÌNH ẢNH HIỆN TẠI --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Hình ảnh hiện tại</label><br>
            @php $images = is_array($product->image) ? $product->image : json_decode($product->image, true); @endphp
            @if ($images)
                <div class="d-flex gap-2 flex-wrap mb-2" id="existing-images">
                    @foreach ($images as $img)
                        <div class="position-relative image-wrapper">
                            <img src="{{ asset('images/' . $img) }}" class="border"
                                style="width:80px; height:80px; object-fit:cover;">
                            <input type="hidden" name="existing_images[]" value="{{ $img }}">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 delete-image-btn"
                                title="Xóa ảnh" aria-label="Xóa ảnh" style="transform: translate(25%, -25%);">&times;</button>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">Chưa có ảnh</p>
            @endif
        </div>

        {{-- HÌNH ẢNH MỚi --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Cập nhật hình ảnh mới (tối đa 4 ảnh)</label>
            <div id="preview-images" class="mb-2 d-flex gap-2 flex-wrap"></div>
            <input type="file" name="new_images[]" class="form-control" accept="image/*" multiple id="input-images">
            <div class="form-text">Chọn tối đa 4 ảnh. Ảnh đầu tiên sẽ là ảnh đại diện.</div>
            @error('images_required')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('products.my') }}" class="btn btn-secondary px-5 me-2">Hủy chỉnh sửa</a>
            <button type="submit" class="btn btn-primary px-5">Cập nhật và tự động gia hạn</button>
        </div>
    </form>
</div>

<x-slot:js>
    <script>
        // Xem trước ảnh mới
        document.getElementById('input-images').addEventListener('change', function () {
            const preview = document.getElementById('preview-images');
            preview.innerHTML = '';

            const existing = document.querySelectorAll('input[name="existing_images[]"]').length;
            const maxNew = 4 - existing;

            const files = Array.from(this.files).slice(0, maxNew);
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'border p-1 bg-light';
                        img.style.width = '80px';
                        img.style.height = '80px';
                        img.style.objectFit = 'cover';
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                }
            });

            if (this.files.length > maxNew) {
                alert(`Bạn chỉ có thể thêm tối đa ${maxNew} ảnh mới.`);
            }
        });

        // Ẩn/hiện ô nhập giá theo deal_type
        function togglePriceInput() {
            const priceGroup = document.getElementById('price-input-group');
            const selected = document.querySelector('input[name="deal_type"]:checked');
            if (selected && selected.value === 'price') {
                priceGroup.style.display = 'block';
                priceGroup.querySelector('input').required = true;
            } else {
                priceGroup.style.display = 'none';
                priceGroup.querySelector('input').required = false;
            }
        }

        document.querySelectorAll('input[name="deal_type"]').forEach(radio => {
            radio.addEventListener('change', togglePriceInput);
        });

        document.addEventListener('DOMContentLoaded', togglePriceInput);

        // Trước khi submit: bắt buộc có ít nhất 1 ảnh
        document.querySelector('form').addEventListener('submit', function (e) {
            const existing = document.querySelectorAll('input[name="existing_images[]"]').length;
            const newImgs = document.getElementById('input-images').files.length;

            // Nếu KHÔNG còn ảnh cũ và KHÔNG có ảnh mới được chọn => không cho submit
            if (existing === 0 && newImgs === 0) {
                e.preventDefault();
                alert('Sản phẩm phải có ít nhất một hình ảnh. Vui lòng chọn ảnh mới.');
                return;
            }

            // Nếu ảnh mới vượt quá giới hạn
            if (newImgs > (4 - existing)) {
                e.preventDefault();
                alert(`Bạn chỉ có thể thêm tối đa ${4 - existing} ảnh mới.`);
                return;
            }
        });


        // Hàm cập nhật lại input existing_images[] sau khi xóa ảnh
        function refreshExistingImageInputs() {
            const container = document.getElementById('existing-images');
            const images = container.querySelectorAll('.image-wrapper');

            container.querySelectorAll('input[name="existing_images[]"]').forEach(input => input.remove());

            images.forEach(wrapper => {
                const img = wrapper.querySelector('img');
                const src = img.getAttribute('src');
                const filename = src.split('/').pop();

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'existing_images[]';
                input.value = filename;
                wrapper.appendChild(input);
            });
        }

        // Xóa ảnh qua AJAX
        document.querySelectorAll('.delete-image-btn').forEach(button => {
            button.addEventListener('click', function () {
                const wrapper = this.closest('.image-wrapper');
                const img = wrapper.querySelector('img');
                const filename = img.getAttribute('src').split('/').pop();
                const productId = {{ $product->id }};
                const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
                const deleteUrl = isAdmin
                    ? `/admin/products/${productId}/images`
                    : `/products/${productId}/images`;

                if (confirm('Bạn có chắc chắn muốn xóa ảnh này không?')) {
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ image: filename })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                wrapper.remove();
                                refreshExistingImageInputs();
                            } else {
                                alert('Lỗi khi xóa ảnh');
                            }
                        })
                        .catch(() => alert('Lỗi kết nối khi xóa ảnh.'));
                }
            });
        });
    </script>
</x-slot:js>