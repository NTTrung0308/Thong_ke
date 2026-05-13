<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>403 - Truy cập bị từ chối</title>
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
            color: #f3545d; /* Danger color for 403 */
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
            box-shadow: 0 5px 15px rgba(243, 84, 93, 0.3);
        }
    </style>
</head>
<body>
    <div class="error-wrapper">
        <div class="error-code">403</div>
        <div class="error-message">Truy cập bị từ chối</div>
        <div class="error-description">
            Bạn không có quyền truy cập vào tài nguyên này. 
            Nếu bạn cho rằng đây là một sự nhầm lẫn, vui lòng liên hệ với quản trị viên hệ thống.
        </div>
        <a href="{{ url('/') }}" class="btn btn-danger btn-back-home">
            <i class="fas fa-home me-2"></i> Quay về trang chủ
        </a>
    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('backend/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/core/bootstrap.min.js') }}"></script>
</body>
</html>
