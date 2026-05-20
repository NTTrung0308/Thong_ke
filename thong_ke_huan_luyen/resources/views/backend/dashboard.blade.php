@extends('backend.layouts.dashboard')

@section('dashboard_content')
<div class="row mt-4">
    <div class="col-md-12 text-center">
        <h1 class="fw-extrabold mb-1" style="color: #1a2035;">HỆ THỐNG THỐNG KÊ HUẤN LUYỆN</h1>
        <p class="text-muted mb-5">TRUNG TÂM ĐIỀU HÀNH & CHỈ HUY CHIẾN ĐẤU</p>
    </div>
</div>

<div class="container py-3">
    <!-- Step 1: Chọn Cấp Quản Lý -->
    <div id="step-levels" class="row justify-content-center g-4 animate__animated animate__fadeIn">
        <div class="col-12 text-center mb-3">
            <h4 class="fw-bold"><i class="fas fa-layer-group me-2 text-primary"></i> BƯỚC 1: CHỌN CẤP QUẢN LÝ</h4>
        </div>
        
        @php
            $levels = [
                'chi-huy' => ['label' => 'CẤP CHỈ HUY', 'icon' => 'fa-star', 'color' => 'var(--primary-gradient)'],
                'trung-doan' => ['label' => 'CẤP TRUNG ĐOÀN', 'icon' => 'fa-shield-alt', 'color' => 'var(--secondary-gradient)'],
                'tieu-doan' => ['label' => 'CẤP TIỂU ĐOÀN', 'icon' => 'fa-fort-awesome', 'color' => 'var(--info-gradient)'],
                'dai-doi' => ['label' => 'CẤP ĐẠI ĐỘI', 'icon' => 'fa-users', 'color' => 'var(--success-gradient)'],
                'trung-doi' => ['label' => 'CẤP TRUNG ĐỘI', 'icon' => 'fa-user-friends', 'color' => 'var(--warning-gradient)']
            ];

            $user = auth()->user();
            $maxLevelOrder = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
            
            $userMaxLevel = 'trung-doi';
            if ($user->hasRole('chi-huy')) $userMaxLevel = 'chi-huy';
            elseif ($user->hasRole('trung-doan')) $userMaxLevel = 'trung-doan';
            elseif ($user->hasRole('tieu-doan')) $userMaxLevel = 'tieu-doan';
            elseif ($user->hasRole('dai-doi')) $userMaxLevel = 'dai-doi';

            $startIndex = array_search($userMaxLevel, $maxLevelOrder);
            $visibleLevels = array_slice($maxLevelOrder, $startIndex);
        @endphp

        @foreach($visibleLevels as $levelKey)
            <div class="col-md-4 col-lg-2">
                <button class="btn-floating-nav level-btn w-100" data-level="{{ $levelKey }}" style="background: {{ $levels[$levelKey]['color'] }}">
                    <div class="icon-wrap">
                        <i class="fas {{ $levels[$levelKey]['icon'] }}"></i>
                    </div>
                    <span>{{ $levels[$levelKey]['label'] }}</span>
                </button>
            </div>
        @endforeach
    </div>

    <!-- Step 2: Chọn Tính Năng (Mặc định ẩn) -->
    <div id="step-modules" class="row justify-content-center g-4 d-none animate__animated animate__fadeIn">
        <div class="col-12 text-center mb-3">
            <div class="d-flex align-items-center justify-content-center gap-3">
                <button id="back-to-levels" class="btn btn-sm btn-outline-secondary btn-round">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </button>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-th-large me-2 text-primary"></i> 
                    BƯỚC 2: CHỌN CHỨC NĂNG QUẢN LÝ - <span id="selected-level-label" class="text-primary"></span>
                </h4>
            </div>
        </div>

        @php
            $modules = [
                ['label' => 'DANH SÁCH QUÂN NHÂN', 'icon' => 'fa-user-tie', 'route' => 'soldiers.index', 'special' => 'soldier-menu', 'color' => 'var(--primary-gradient)'],
                ['label' => 'VŨ KHÍ TRANG BỊ', 'icon' => 'fa-crosshairs', 'route' => 'weapon-equipments.index', 'special' => 'weapon-menu', 'color' => 'var(--danger-gradient)'],
                ['label' => 'KHEN THƯỞNG', 'icon' => 'fa-medal', 'route' => 'rewards.index', 'color' => 'var(--success-gradient)'],
                ['label' => 'KỶ LUẬT', 'icon' => 'fa-exclamation-triangle', 'route' => 'disciplines.index', 'color' => 'var(--warning-gradient)'],
                ['label' => 'KẾT QUẢ TẬP HUẤN', 'icon' => 'fa-chart-bar', 'route' => 'training-results.index', 'color' => 'var(--info-gradient)'],
                ['label' => 'NHẬT KÝ HUẤN LUYỆN', 'icon' => 'fa-book', 'route' => 'training-logs.index', 'color' => 'var(--secondary-gradient)']
            ];
        @endphp

        @foreach($modules as $module)
            <div class="col-md-4 col-lg-4">
                <a href="#" class="module-link" data-base-url="{{ route($module['route']) }}" data-special="{{ $module['special'] ?? '' }}">
                    <div class="btn-floating-nav w-100" style="background: {{ $module['color'] }}; height: 180px;">
                        <div class="icon-wrap" style="font-size: 3rem;">
                            <i class="fas {{ $module['icon'] }}"></i>
                        </div>
                        <span class="fs-5">{{ $module['label'] }}</span>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Step 3: Chọn Hình Thức Hiển Thị (Dành riêng cho Quân nhân) -->
    <div id="step-soldier-options" class="row justify-content-center g-4 d-none animate__animated animate__fadeIn">
        <div class="col-12 text-center mb-3">
            <div class="d-flex align-items-center justify-content-center gap-3">
                <button class="btn btn-sm btn-outline-secondary btn-round back-to-modules">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </button>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-users me-2 text-primary"></i> 
                    BƯỚC 3: CHỌN HÌNH THỨC HIỂN THỊ - QUÂN NHÂN
                </h4>
            </div>
        </div>

        <div class="col-md-5">
            <a href="#" class="final-option-link" data-route="{{ route('soldiers.index') }}">
                <div class="btn-floating-nav w-100" style="background: var(--primary-gradient); height: 220px;">
                    <div class="icon-wrap">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="fs-4">XEM TOÀN BỘ</span>
                    <p class="text-white opacity-75 small">Hiển thị danh sách quân nhân theo đơn vị</p>
                </div>
            </a>
        </div>

        <div class="col-md-5">
            <a href="#" class="final-option-link" data-route="{{ route('search.index') }}">
                <div class="btn-floating-nav w-100" style="background: var(--info-gradient); height: 220px;">
                    <div class="icon-wrap">
                        <i class="fas fa-search"></i>
                    </div>
                    <span class="fs-4">TÌM KIẾM</span>
                    <p class="text-white opacity-75 small">Tra cứu quân nhân nâng cao và kiểm tra</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Step 3: Chọn Hình Thức Hiển Thị (Dành riêng cho Vũ khí) -->
    <div id="step-weapon-options" class="row justify-content-center g-4 d-none animate__animated animate__fadeIn">
        <div class="col-12 text-center mb-3">
            <div class="d-flex align-items-center justify-content-center gap-3">
                <button class="btn btn-sm btn-outline-secondary btn-round back-to-modules">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </button>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-crosshairs me-2 text-danger"></i> 
                    BƯỚC 3: CHỌN HÌNH THỨC HIỂN THỊ - VŨ KHÍ TRANG BỊ
                </h4>
            </div>
        </div>

        <div class="col-md-5">
            <a href="#" class="final-option-link" data-route="{{ route('weapon-equipments.index') }}">
                <div class="btn-floating-nav w-100" style="background: var(--danger-gradient); height: 220px;">
                    <div class="icon-wrap">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <span class="fs-4">XEM TOÀN BỘ</span>
                    <p class="text-white opacity-75 small">Bảng thống kê vũ khí trang bị chi tiết</p>
                </div>
            </a>
        </div>

        <div class="col-md-5">
            <a href="#" class="final-option-link" data-route="{{ route('search.index') }}">
                <div class="btn-floating-nav w-100" style="background: var(--info-gradient); height: 220px;">
                    <div class="icon-wrap">
                        <i class="fas fa-search"></i>
                    </div>
                    <span class="fs-4">TRA CỨU SỐ HIỆU</span>
                    <p class="text-white opacity-75 small">Tìm kiếm theo số hiệu súng, loại vũ khí</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Thống kê nhanh ở cuối trang -->
<div class="row mt-5 px-4">
    <div class="col-md-12">
        <div class="card card-round shadow-sm">
            <div class="card-body">
                <div class="row text-center py-2">
                    <div class="col-md-3 border-end">
                        <h3 class="fw-bold text-primary">{{ number_format($totalSoldiers) }}</h3>
                        <p class="text-muted mb-0">Tổng quân số</p>
                    </div>
                    <div class="col-md-3 border-end">
                        <h3 class="fw-bold text-danger">{{ number_format($totalWeapons) }}</h3>
                        <p class="text-muted mb-0">Trang bị vũ khí</p>
                    </div>
                    <div class="col-md-3 border-end">
                        <h3 class="fw-bold text-success">{{ number_format($totalRewards) }}</h3>
                        <p class="text-muted mb-0">Khen thưởng năm</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="fw-bold text-warning">{{ number_format($totalDisciplines) }}</h3>
                        <p class="text-muted mb-0">Kỷ luật đang thi hành</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<script>
    $(document).ready(function() {
        let currentLevel = '';

        $('.level-btn').on('click', function() {
            currentLevel = $(this).data('level');
            const levelLabel = $(this).find('span').text();
            
            $('#selected-level-label').text(levelLabel);
            
            // Chuyển bước 1 -> 2
            $('#step-levels').addClass('animate__fadeOutLeft');
            setTimeout(() => {
                $('#step-levels').addClass('d-none');
                $('#step-modules').removeClass('d-none').addClass('animate__fadeInRight');
            }, 500);
        });

        $('#back-to-levels').on('click', function() {
            // Quay lại bước 2 -> 1
            $('#step-modules').addClass('animate__fadeOutRight');
            setTimeout(() => {
                $('#step-modules').addClass('d-none');
                $('#step-levels').removeClass('d-none').addClass('animate__fadeInLeft').removeClass('animate__fadeOutLeft');
            }, 500);
        });

        $('.module-link').on('click', function(e) {
            e.preventDefault();
            const special = $(this).data('special');
            const baseUrl = $(this).data('base-url');

            if (special === 'soldier-menu') {
                // Chuyển bước 2 -> 3 (Soldier)
                $('#step-modules').addClass('animate__fadeOutLeft');
                setTimeout(() => {
                    $('#step-modules').addClass('d-none');
                    $('#step-soldier-options').removeClass('d-none').addClass('animate__fadeInRight');
                }, 500);
            } else if (special === 'weapon-menu') {
                // Chuyển bước 2 -> 3 (Weapon)
                if ($('#step-weapon-options').length) {
                    $('#step-modules').addClass('animate__fadeOutLeft');
                    setTimeout(() => {
                        $('#step-modules').addClass('d-none');
                        $('#step-weapon-options').removeClass('d-none').addClass('animate__fadeInRight');
                    }, 500);
                } else {
                    const finalUrl = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'level=' + currentLevel;
                    window.location.href = finalUrl;
                }
            } else {
                // Chuyển hướng trực tiếp cho các module khác
                const finalUrl = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'level=' + currentLevel;
                window.location.href = finalUrl;
            }
        });

        $('.back-to-modules').on('click', function() {
            // Quay lại bước 3 -> 2
            const currentStep = $(this).closest('.row');
            currentStep.addClass('animate__fadeOutRight');
            setTimeout(() => {
                currentStep.addClass('d-none');
                $('#step-modules').removeClass('d-none').addClass('animate__fadeInLeft').removeClass('animate__fadeOutLeft');
            }, 500);
        });

        $('.final-option-link').on('click', function(e) {
            e.preventDefault();
            const baseUrl = $(this).data('route');
            const finalUrl = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'level=' + currentLevel;
            window.location.href = finalUrl;
        });
    });
</script>

<style>
    .btn-floating-nav {
        border: none;
        border-radius: 20px;
        padding: 30px 20px;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 15px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        height: 220px;
    }

    .btn-floating-nav:hover {
        transform: translateY(-10px) scale(1.05);
        box-shadow: 0 15px 30px rgba(0,0,0,0.3);
    }

    .btn-floating-nav .icon-wrap {
        font-size: 4rem;
        background: rgba(255,255,255,0.2);
        width: 100px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .btn-floating-nav:hover .icon-wrap {
        background: rgba(255,255,255,0.4);
        transform: rotate(10deg);
    }

    .btn-floating-nav span {
        font-weight: 800;
        letter-spacing: 1px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .module-link {
        text-decoration: none !important;
    }

    /* Override cho các màn hình nhỏ */
    @media (max-width: 768px) {
        .btn-floating-nav {
            height: 150px;
            padding: 15px;
        }
        .btn-floating-nav .icon-wrap {
            width: 60px;
            height: 60px;
            font-size: 2rem !important;
        }
    }
</style>
@endsection
