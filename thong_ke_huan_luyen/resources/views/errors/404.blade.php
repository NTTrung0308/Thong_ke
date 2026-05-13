<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>404 - Không tìm thấy trang</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" href="{{ asset('backend/assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon"/>

    <!-- Fonts and icons -->
    <script src="{{ asset('backend/assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: {"families":["Public Sans:300,400,500,600,700"]},
            custom: {"families":["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ["{{ asset('backend/assets/css/fonts.min.css') }}"]},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/kaiadmin.min.css') }}">

    <style>
        body {
            background: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-wrapper {
            text-align: center;
            max-width: 600px;
            padding: 20px;
        }
        .error-code {
            font-size: 150px;
            font-weight: 700;
            color: #1a2035;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 4px 4px 10px rgba(0,0,0,0.1);
        }
        .error-message {
            font-size: 24px;
            font-weight: 600;
            color: #575962;
            margin-bottom: 15px;
        }
        .error-description {
            color: #8d9498;
            margin-bottom: 30px;
        }
        .btn-back-home {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .btn-back-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(29, 122, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="error-wrapper">
        <div class="error-code">404</div>
        <div class="error-message">Rất tiếc! Không tìm thấy trang</div>
        <div class="error-description">
            Trang bạn đang tìm kiếm có thể đã bị xóa, thay đổi tên hoặc tạm thời không khả dụng. 
            Vui lòng kiểm tra lại đường dẫn hoặc quay về trang chủ.
        </div>
        <a href="{{ url('/') }}" class="btn btn-primary btn-back-home">
            <i class="fas fa-home me-2"></i> Quay về trang chủ
        </a>
    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('backend/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/core/bootstrap.min.js') }}"></script>
</body>
</html>
