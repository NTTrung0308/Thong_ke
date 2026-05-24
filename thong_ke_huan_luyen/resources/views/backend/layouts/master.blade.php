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

        /* Elevated Cards */
        .card {
            border: none;
            border-radius: 12px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        }
        .card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        /* Floating & Gradient Buttons */
        .btn-primary { background: var(--primary-gradient) !important; border: none !important; }
        .btn-secondary { background: var(--secondary-gradient) !important; border: none !important; }
        .btn-success { background: var(--success-gradient) !important; border: none !important; }
        .btn-danger { background: var(--danger-gradient) !important; border: none !important; }
        .btn-warning { background: var(--warning-gradient) !important; border: none !important; }
        .btn-info { background: var(--info-gradient) !important; border: none !important; }

        .btn {
            border-radius: 8px !important;
            padding: 8px 18px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .btn:hover {
            box-shadow: 0 7px 14px rgba(0,0,0,0.15);
            transform: translateY(-1px);
        }
        .btn:active {
            transform: translateY(1px);
        }

        /* Floating Action Buttons (FAB) Style for icons */
        .btn-round {
            border-radius: 50px !important;
        }

        /* DataTables Styling */
        table.dataTable thead th {
            background-color: #f8f9fa;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #ebedef !important;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,0.01);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(21, 114, 232, 0.04) !important;
        }

        /* Custom Badge */
        .badge {
            padding: 5px 10px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
        }

        /* Sidebar Styling */
        .sidebar {
            box-shadow: 4px 0 15px rgba(0,0,0,0.05);
        }
        .sidebar .nav > .nav-item.active > a {
            background: rgba(21, 114, 232, 0.08) !important;
            border-radius: 8px;
            margin: 5px 15px;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 25px !important;
        }
        .page-title {
            font-weight: 800 !important;
            color: #1a2035;
        }

        /* Action icons visibility (View / Edit / Delete buttons) */
        .form-button-action .btn-link {
            color: rgba(58, 66, 86, 0.95) !important;
            opacity: 1 !important;
            padding: 6px !important;
            border-radius: 6px !important;
            transition: all 0.12s ease;
        }
        .form-button-action .btn-link .fa, .form-button-action .btn-link .fas, .form-button-action .btn-link .far {
            font-size: 1.05rem !important;
        }
        .form-button-action .btn-link.btn-info {
            color: #0d6efd !important; /* clearer blue for View */
            background: rgba(13,110,253,0.08) !important;
        }
        .form-button-action .btn-link.btn-primary {
            color: #1572E8 !important; /* Edit */
            background: rgba(21,114,232,0.08) !important;
        }
        .form-button-action .btn-link.btn-success {
            color: #198754 !important;
            background: rgba(25,135,84,0.08) !important;
        }
        .form-button-action .btn-link.btn-danger {
            color: #dc3545 !important; /* Delete */
            background: rgba(220,53,69,0.08) !important;
        }
        .form-button-action .btn-link:hover {
            text-decoration: none !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.08);
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
