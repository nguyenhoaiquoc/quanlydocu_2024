
<div class="container pt-5 mt-5">
    <h2 class="mb-4 text-center">📦 Đăng sản phẩm mới</h2>

    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        {{-- DANH MỤC --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Danh mục sản phẩm <span class="text-danger">*</span></label><br>
            @foreach ($categories as $category)
                <input type="checkbox" class="btn-check" id="cat-{{ $category->id }}" name="categories[]"
                    value="{{ $category->id }}">
                <label class="btn btn-outline-primary mb-1 me-1" for="cat-{{ $category->id }}">{{ $category->name }}</label>
            @endforeach
            @error('categories')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror

            {{-- Cho phép nhập danh mục mới --}}
            <div class="mt-2">
                <input type="text" name="new_category" class="form-control" placeholder="Hoặc nhập danh mục mới...">
                <div class="form-text">Bạn có thể tự thêm một danh mục nếu chưa có trong danh sách.</div>
            </div>
        </div>

        {{-- TIÊU ĐỀ --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tiêu đề <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" maxlength="255" required
                placeholder="VD: Bàn học gỗ giá rẻ">
        </div>

        {{-- MÔ TẢ --}}
    <div class="mb-3">
    <label class="form-label fw-bold">Yêu cầu AI (tùy chọn)</label>
    <input type="text" id="ai_prompt" class="form-control" placeholder="VD: Viết mô tả cho bàn học cũ 6 tháng, bằng gỗ, giá 150k">
    <button type="button" class="btn btn-outline-secondary mt-2" onclick="generateDescriptionFromPrompt()">✨ Sinh mô tả từ yêu cầu AI</button>
</div>


        <div class="mb-3">
            
            <label class="form-label fw-bold">Mô tả chi tiết <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" rows="5" maxlength="15000" required
                placeholder="Mô tả tình trạng, tính năng, điểm mạnh của sản phẩm..."></textarea>
        </div>

        {{-- THÔNG TIN CHI TIẾT --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tình trạng</label>
                <select name="condition" class="form-select" required>
                    <option value="">-- Chọn tình trạng --</option>
                    <option value="Mới 100%">Mới 100%</option>
                    <option value="Như mới">Như mới</option>
                    <option value="Đã qua sử dụng">Đã qua sử dụng</option>
                    <option value="Cũ">Cũ</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Chất liệu</label>
                <input type="text" name="material" class="form-control" placeholder="VD: Gỗ, nhựa, kim loại...">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Kích thước</label>
                <input type="text" name="size" class="form-control" placeholder="VD: Dài x Rộng x Cao (cm)">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Thương hiệu</label>
                <input type="text" name="brand" class="form-control" placeholder="VD: IKEA, Samsung, Sony...">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Thời gian sử dụng</label>
                <input type="text" name="used_duration" class="form-control" placeholder="VD: 6 tháng, 1 năm...">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Lý do bán</label>
                <input type="text" name="reason" class="form-control" placeholder="VD: Không dùng nữa, chuyển nhà...">
            </div>
        </div>

        {{-- GIÁ --}}
        {{-- GIÁ / HÌNH THỨC GIAO DỊCH --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Hình thức giao dịch <span class="text-danger">*</span></label>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="deal_type" value="price" id="deal_price" checked>
                <label class="form-check-label" for="deal_price">Bán với giá mong muốn</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="deal_type" value="exchange" id="deal_exchange">
                <label class="form-check-label" for="deal_exchange">Trao đổi</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="deal_type" value="free" id="deal_free">
                <label class="form-check-label" for="deal_free">Miễn phí</label>
            </div>
        </div>

        <div class="mb-3" id="price-input-group">
            <label class="form-label fw-bold">Giá bán mong muốn (VNĐ) <span class="text-danger">*</span></label>
            <input type="number" name="price" class="form-control" min="0" placeholder="VD: 150000">
        </div>

        {{-- THANH TOÁN --}}
        <div class="mb-3" id="checksell">
            <label class="form-label fw-bold">Hình thức thanh toán</label>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_cash"
                    value="Thanh toán bằng tiền mặt" checked>
                <label class="form-check-label" for="pay_cash">Tiền mặt</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_transfer"
                    value="Thanh toán bằng chuyển khoản trực tuyến">
                <label class="form-check-label" for="pay_transfer">Chuyển khoản trực tuyến</label>
            </div>
        </div>

        {{-- ĐỊA ĐIỂM --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Nơi giao dịch chính <span class="text-danger">*</span></label>
            <input type="text" name="location_primary" class="form-control" required
                placeholder="VD: Ký túc xá TDC, Linh Chiểu, Thủ Đức">
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Nơi giao dịch khác (tuỳ chọn)</label>
            <input type="text" name="location_secondary" class="form-control"
                placeholder="VD: Cổng chính trường, khu C, v.v...">
        </div>

        {{-- HÌNH ẢNH --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Hình ảnh sản phẩm <span class="text-danger">*</span></label>
            <div id="preview-images" class="mb-2 d-flex gap-2 flex-wrap"></div>
            <input type="file" name="images[]" class="form-control" accept="image/*" multiple required
                id="input-images">
            <div class="form-text">Chọn tối đa 4 ảnh. Ảnh đầu tiên sẽ là ảnh đại diện.</div>
        </div>

        <div class="text-center mt-4">
            <p class="text-success small">
                ✅ Vui lòng kiểm tra kỹ nội dung và hình ảnh trước khi đăng. Bài viết sẽ được xét duyệt thủ công.
            </p>
            <button type="submit" class="btn btn-success px-5">Đăng bài</button>
        </div>
    </form>
</div>

{{-- SCRIPT --}}
<x-slot:js>
    <script>

      function generateDescriptionFromPrompt() {
    const promptText = document.getElementById('ai_prompt').value.trim();
    const descriptionField = document.querySelector('textarea[name="description"]');

    if (!promptText) {
        alert("Vui lòng nhập yêu cầu AI.");
        return;
    }

    descriptionField.value = "🧠 Đang tạo mô tả từ yêu cầu...";

    fetch("{{ route('ai.generatePrompt') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": '{{ csrf_token() }}'
        },
        body: JSON.stringify({ prompt: promptText })
    })
    .then(res => {
        if (!res.ok) throw new Error("Lỗi " + res.status);
        return res.json();
    })
    .then(res => {
        descriptionField.value = res.description || "⚠️ Không tạo được mô tả. Vui lòng thử lại.";
    })
    .catch(err => {
        console.error(err);
        descriptionField.value = "❌ Lỗi khi gọi AI.";
    });
}


        // Xem trước ảnh sản phẩm (tối đa 4 ảnh)
        document.getElementById('input-images').addEventListener('change', function () {
            const preview = document.getElementById('preview-images');
            preview.innerHTML = '';
            Array.from(this.files).slice(0, 4).forEach(file => {
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
        });

        // Ẩn/hiện ô nhập giá theo hình thức giao dịch
        function togglePriceInput() {
            const priceGroup = document.getElementById('price-input-group');
               const checkSell = document.getElementById('checksell');
            const selected = document.querySelector('input[name="deal_type"]:checked');
            if (selected && selected.value === 'price') {
                priceGroup.style.display = 'block';
                priceGroup.querySelector('input').required = true;
            } else {
                checkSell.style.display = 'none';
                priceGroup.style.display = 'none';
                priceGroup.querySelector('input').required = false;
            }
        }

        document.querySelectorAll('input[name="deal_type"]').forEach(radio => {
            radio.addEventListener('change', togglePriceInput);
        });

        // Gọi khi tải trang lần đầu
        document.addEventListener('DOMContentLoaded', togglePriceInput);
    </script>
</x-slot:js>