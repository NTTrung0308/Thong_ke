<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hệ thống Thống kê Huấn luyện Chiến Đấu</title>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <link rel="icon" href="{{ asset('backend/assets/img/kaiadmin/logo.png') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('backend/assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('backend/assets/css/fonts.min.css') }}"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/kaiadmin.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        /* Modern UI Tweaks */
        :root {
            --primary-gradient: linear-gradient(135deg, #1572e8 0%, #0c59b8 100%);
            --secondary-gradient: linear-gradient(135deg, #6861ce 0%, #5044b5 100%);
            --success-gradient: linear-gradient(135deg, #31ce36 0%, #249b28 100%);
            --danger-gradient: linear-gradient(135deg, #f25961 0%, #d13038 100%);
            --warning-gradient: linear-gradient(135deg, #ffad46 0%, #e68a00 100%);
            --info-gradient: linear-gradient(135deg, #48abf7 0%, #1a91da 100%);
        }

        /* Page Loading Animation */
        .page-inner {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Elevated Cards */
        .card {
            border: none;
            border-radius: 15px !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            overflow: hidden;
        }
        .card:hover {
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
            transform: translateY(-5px);
        }

        /* Floating & Gradient Buttons */
        .btn-primary { background: var(--primary-gradient) !important; border: none !important; }
        .btn-secondary { background: var(--secondary-gradient) !important; border: none !important; }
        .btn-success { background: var(--success-gradient) !important; border: none !important; }
        .btn-danger { background: var(--danger-gradient) !important; border: none !important; }
        .btn-warning { background: var(--warning-gradient) !important; border: none !important; }
        .btn-info { background: var(--info-gradient) !important; border: none !important; }

        .btn {
            border-radius: 10px !important;
            padding: 10px 22px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn:hover {
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
            transform: translateY(-2px) scale(1.02);
        }
        .btn:active {
            transform: translateY(0) scale(0.98);
        }

        /* Sidebar Item Hover Animation */
        .sidebar .nav > .nav-item a {
            transition: all 0.3s ease;
        }
        .sidebar .nav > .nav-item a:hover {
            background: rgba(255, 255, 255, 0.05) !important;
            padding-left: 30px !important;
        }
        .sidebar .nav > .nav-item.active > a {
            background: var(--primary-gradient) !important;
            box-shadow: 0 4px 15px rgba(21, 114, 232, 0.4);
            border-radius: 10px;
            margin: 5px 15px;
        }

        /* Table Row Hover Animation */
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(21, 114, 232, 0.06) !important;
            transform: scale(1.005);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            z-index: 1;
            position: relative;
        }

        /* Icon Spin on Hover */
        .btn:hover i {
            animation: fa-spin 2s infinite linear;
        }
        .btn-no-spin:hover i {
            animation: none;
        }

        /* Animation Delays */
        .delay-1 { animation-delay: 0.1s !important; }
        .delay-2 { animation-delay: 0.2s !important; }
        .delay-3 { animation-delay: 0.3s !important; }
        .delay-4 { animation-delay: 0.4s !important; }
        .delay-5 { animation-delay: 0.5s !important; }
        .delay-6 { animation-delay: 0.6s !important; }

        /* Custom Floating Nav Buttons (Dashboard) */
        .btn-floating-nav {
            border: none;
            border-radius: 20px;
            color: white;
            padding: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .btn-floating-nav:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 30px rgba(0,0,0,0.2);
        }
        .btn-floating-nav .icon-wrap {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .btn-floating-nav:hover .icon-wrap {
            background: rgba(255,255,255,0.4);
            transform: rotate(15deg);
        }

        /* Custom Scrollbar for Smoothness */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        @include('backend.include.sidebar')
        @yield('content')
        @include('backend.include.dashboard_modal')
    </div>
    <!--   Core JS Files   -->
    <script src="{{ asset('backend/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('backend/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('backend/assets/js/plugin/chart.js/chart.min.js') }}"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('backend/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('backend/assets/js/plugin/chart-circle/circles.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('backend/assets/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('backend/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset('backend/assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/jsvectormap/world.js') }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('backend/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('backend/assets/js/kaiadmin.min.js') }}"></script>

    {{-- <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="{{ asset('backend/assets/js/setting-demo.js') }}"></script>
    <script src="{{ asset('backend/assets/js/demo.js') }}"></script> --}}

    <script>
        // Cấu hình SweetAlert2 để tự động hiển thị thông báo từ session flash của Laravel
        $(document).ready(function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: '{{ session('error') }}',
                    timer: 5000
                });
            @endif

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Cảnh báo!',
                    text: '{{ session('warning') }}'
                });
            @endif

            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Thông báo',
                    text: '{{ session('info') }}'
                });
            @endif

            @if (session('show_dashboard'))
                var myModal = new bootstrap.Modal(document.getElementById('dashboardModal'));
                myModal.show();
            @endif

            // Xử lý click thông báo
            $('.notification-item').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var link = $(this).attr('href');
                var self = $(this);

                $.ajax({
                    url: '/notifications/mark-as-read/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        window.location.href = link;
                    },
                    error: function() {
                        window.location.href = link;
                    }
                });
            });
        });

        // Tự động thu gọn sidebar để không gian làm việc rộng rãi hơn
        $(function() {
            const autoCollapseDelay = 500;
            let collapseTimer = null;
            let manualToggled = false;

            const collapseSidebar = () => {
                if (!$('.wrapper').hasClass('sidebar_minimize')) {
                    $('.wrapper').addClass('sidebar_minimize');
                    $('.toggle-sidebar').addClass('toggled').html('<i class="gg-more-vertical-alt"></i>');
                }
            };

            const expandSidebar = () => {
                if ($('.wrapper').hasClass('sidebar_minimize')) {
                    $('.wrapper').removeClass('sidebar_minimize');
                    $('.toggle-sidebar').removeClass('toggled').html('<i class="gg-menu-right"></i>');
                }
            };

            // Mặc định thu gọn
            collapseSidebar();

            $('.sidebar').on('mouseenter', function() {
                clearTimeout(collapseTimer);
                if (!manualToggled) expandSidebar();
            }).on('mouseleave', function() {
                clearTimeout(collapseTimer);
                if (!manualToggled) collapseTimer = setTimeout(collapseSidebar, autoCollapseDelay);
            });

            $('.toggle-sidebar').on('click', function() {
                setTimeout(() => { manualToggled = !$('.wrapper').hasClass('sidebar_minimize'); }, 50);
            });
        });
    </script>
    @yield('scripts')
</body>

</html>
