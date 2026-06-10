<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nhập mã xác minh</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            background: #f8f9fa;
        }
        .verify-card {
            max-width: 420px;
            margin: 60px auto;
            padding: 30px;
            border-radius: 12px;
            background-color: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }
        .verify-card h4 {
            font-weight: 600;
            color: #4f46e5;
        }
        .form-control {
            border-radius: 8px;
        }
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="verify-card">
            <h4 class="text-center mb-4">
                <i class="bi bi-shield-lock-fill me-2"></i>Nhập mã xác minh
            </h4>

            <p class="text-center text-muted mb-4">
                Vui lòng nhập mã xác minh đã gửi tới email của bạn để tiếp tục.
            </p>

            <form method="POST" action="{{ route('auth.verify-code') }}">
                @csrf

                <div class="mb-3">
                    <label for="code" class="form-label">Mã xác nhận</label>
                    <input type="text" name="code" id="code" class="form-control" placeholder="Nhập mã gồm 6 chữ số" required>
                    @error('code')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        Xác nhận
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
