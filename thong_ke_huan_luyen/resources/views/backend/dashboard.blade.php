@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="row mt-4 mb-3">
        <div class="col-md-8">
            <div class="animate__animated animate__fadeInDown">
                <h1 class="fw-extrabold mb-1" style="color: #1a2035; letter-spacing: -1px;">HỆ THỐNG THỐNG KÊ HUẤN LUYỆN</h1>
            </div>
        </div>
        <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end mt-3 mt-md-0">
            <div class="quick-actions animate__animated animate__fadeInRight">
                <a href="{{ route('soldiers.index') }}" class="btn btn-primary btn-round shadow-sm me-2">
                    <i class="fas fa-plus me-1"></i> Thêm hồ sơ
                </a>
                <button class="btn btn-white btn-round shadow-sm border" id="btn-refresh-dashboard">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid py-2">
        <!-- Breadcrumb điều hướng -->
        <div id="navigation-breadcrumb" class="mb-4 d-none animate__animated animate__fadeIn">
            <div class="d-flex align-items-center justify-content-between">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 shadow-sm px-4 py-2 bg-white rounded-pill mb-0">
                        <li class="breadcrumb-item"><a href="#" id="breadcrumb-home"><i class="fas fa-home"></i> Cấp</a></li>
                        <li class="breadcrumb-item d-none" id="breadcrumb-level"></li>
                        <li class="breadcrumb-item d-none" id="breadcrumb-unit"></li>
                        <li class="breadcrumb-item d-none" id="breadcrumb-module"></li>
                    </ol>
                </nav>
                <button class="btn btn-sm btn-outline-danger btn-round ms-3" id="btn-reset-wizard">
                    <i class="fas fa-undo me-1"></i> Làm lại
                </button>
            </div>
        </div>

        <!-- Step 1: Chọn Cấp Quản Lý -->
        <div id="step-levels" class="row justify-content-center g-4 animate__animated animate__fadeIn">
            <div class="col-12 text-center mb-2">
                <h4 class="fw-bold text-uppercase"><i class="fas fa-layer-group me-2 text-primary"></i>Chọn Cấp
                    Quản Lý</h4>
            </div>

            @php
                $levels = [
                    'chi-huy' => ['label' => 'CẤP CHỈ HUY', 'icon' => 'fa-star', 'color' => 'var(--primary-gradient)'],
                    'trung-doan' => [
                        'label' => 'CẤP TRUNG ĐOÀN',
                        'icon' => 'fa-shield-alt',
                        'color' => 'var(--secondary-gradient)',
                    ],
                    'tieu-doan' => [
                        'label' => 'CẤP TIỂU ĐOÀN',
                        'icon' => 'fa-building-user',
                        'color' => 'var(--info-gradient)',
                    ],
                    'dai-doi' => ['label' => 'CẤP ĐẠI ĐỘI', 'icon' => 'fa-users', 'color' => 'var(--success-gradient)'],
                    'trung-doi' => [
                        'label' => 'CẤP TRUNG ĐỘI',
                        'icon' => 'fa-user-friends',
                        'color' => 'var(--warning-gradient)',
                    ],
                ];

                $user = auth()->user();
                $maxLevelOrder = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
                $userMaxLevel = 'trung-doi';

                if ($user->hasRole('chi-huy')) {
                    $userMaxLevel = 'chi-huy';
                } elseif ($user->unit) {
                    $userMaxLevel = $user->unit->level;
                } else {
                    if ($user->hasRole('trung-doan')) {
                        $userMaxLevel = 'trung-doan';
                    } elseif ($user->hasRole('tieu-doan')) {
                        $userMaxLevel = 'tieu-doan';
                    } elseif ($user->hasRole('dai-doi')) {
                        $userMaxLevel = 'dai-doi';
                    } elseif ($user->hasRole('trung-doi')) {
                        $userMaxLevel = 'trung-doi';
                    }
                }

                $startIndex = array_search($userMaxLevel, $maxLevelOrder);
                if ($startIndex === false) {
                    $startIndex = count($maxLevelOrder) - 1;
                }
                $visibleLevels = array_slice($maxLevelOrder, $startIndex);
            @endphp

            @foreach ($visibleLevels as $levelKey)
                <div class="col-md-4 col-lg-2">
                    <button class="btn-floating-nav level-btn w-100 h-100" data-level="{{ $levelKey }}"
                        style="background: {{ $levels[$levelKey]['color'] }}">
                        <div class="icon-wrap">
                            <i class="fas {{ $levels[$levelKey]['icon'] }}"></i>
                        </div>
                        <span>{{ $levels[$levelKey]['label'] }}</span>
                    </button>
                </div>
            @endforeach
        </div>

        <!-- Step 2: Chọn Đơn Vị -->
        <div id="step-units" class="row justify-content-center g-4 d-none animate__animated">
            <div class="col-12 text-center mb-2">
                <h4 class="fw-bold text-uppercase">
                    <i class="fas fa-sitemap me-2 text-primary"></i>Chọn Đơn Vị - <span
                        id="selected-level-label-units" class="text-primary"></span>
                </h4>
            </div>
            <div id="units-container" class="row justify-content-center g-3 w-100 px-4 mt-2">
                <!-- Units will be loaded here -->
            </div>
        </div>

        <!-- Step 3: Chọn Tính Năng -->
        <div id="step-modules" class="row justify-content-center g-4 d-none animate__animated">
            <div class="col-12 text-center mb-2">
                <h4 class="fw-bold text-uppercase">
                    <i class="fas fa-th-large me-2 text-primary"></i>Chọn Chức Năng - <span
                        id="selected-unit-label" class="text-primary"></span>
                </h4>
            </div>

            @php
                $modules = [
                    [
                        'id' => 'soldiers',
                        'label' => 'QUÂN NHÂN',
                        'icon' => 'fa-user-tie',
                        'route' => 'soldiers.index',
                        'color' => 'var(--primary-gradient)',
                        'desc' => 'Quản lý hồ sơ quân nhân',
                    ],
                    [
                        'id' => 'weapons',
                        'label' => 'VŨ KHÍ',
                        'icon' => 'fa-crosshairs',
                        'route' => 'weapon-equipments.index',
                        'color' => 'var(--danger-gradient)',
                        'desc' => 'Quản lý VK-TBKT',
                    ],
                    [
                        'id' => 'rewards',
                        'label' => 'KHEN THƯỞNG',
                        'icon' => 'fa-medal',
                        'route' => 'rewards.index',
                        'color' => 'var(--success-gradient)',
                        'desc' => 'Quản lý khen thưởng',
                    ],
                    [
                        'id' => 'disciplines',
                        'label' => 'KỶ LUẬT',
                        'icon' => 'fa-exclamation-triangle',
                        'route' => 'disciplines.index',
                        'color' => 'var(--warning-gradient)',
                        'desc' => 'Quản lý kỷ luật',
                    ],
                    [
                        'id' => 'training',
                        'label' => 'KẾT QUẢ HL',
                        'icon' => 'fa-chart-bar',
                        'route' => 'training-results.index',
                        'color' => 'var(--info-gradient)',
                        'desc' => 'Kết quả tập huấn',
                    ],
                    [
                        'id' => 'logs',
                        'label' => 'NHẬT KÝ',
                        'icon' => 'fa-book',
                        'route' => 'training-logs.index',
                        'color' => 'var(--secondary-gradient)',
                        'desc' => 'Nhật ký huấn luyện',
                    ],
                ];
            @endphp

            @foreach ($modules as $module)
                <div class="col-md-4 col-lg-2">
                    <button class="btn-floating-nav module-btn w-100" data-id="{{ $module['id'] }}"
                        data-route="{{ route($module['route']) }}" data-label="{{ $module['label'] }}"
                        style="background: {{ $module['color'] }}; height: 200px;">
                        <div class="icon-wrap" style="font-size: 2.5rem; width: 80px; height: 80px;">
                            <i class="fas {{ $module['icon'] }}"></i>
                        </div>
                        <span class="fs-6 mt-1">{{ $module['label'] }}</span>
                        <p class="small text-white opacity-75 mb-0 text-center d-none d-lg-block">{{ $module['desc'] }}</p>
                    </button>
                </div>
            @endforeach
        </div>

        <!-- Step 4: Chọn Hình Thức Hiển Thị (Dynamic) -->
        <div id="step-final" class="row justify-content-center g-4 d-none animate__animated">
            <div class="col-12 text-center mb-2">
                <h4 class="fw-bold text-uppercase">
                    <i class="fas fa-eye me-2 text-primary"></i> Bước 4: Chọn Hình Thức Hiển Thị - <span
                        id="selected-module-label" class="text-primary"></span>
                </h4>
            </div>

            <div class="col-md-4">
                <a href="#" class="final-option-link w-100" id="final-all-link">
                    <div class="btn-floating-nav w-100" id="final-all-card" style="height: 240px;">
                        <div class="icon-wrap">
                            <i class="fas fa-list-ul"></i>
                        </div>
                        <span class="fs-4 mt-2">XEM TOÀN BỘ</span>
                        <p class="text-white opacity-75 small text-center" id="final-all-desc">Hiển thị toàn bộ danh sách dữ
                            liệu</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="#" class="final-option-link w-100" id="final-search-link">
                    <div class="btn-floating-nav w-100" id="final-search-card"
                        style="background: var(--info-gradient); height: 240px;">
                        <div class="icon-wrap">
                            <i class="fas fa-search"></i>
                        </div>
                        <span class="fs-4 mt-2">TÌM KIẾM</span>
                        <p class="text-white opacity-75 small text-center" id="final-search-desc">Tra cứu và lọc dữ liệu
                            nâng cao</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Dữ liệu Biểu đồ & Hoạt động -->
    <div class="row mt-5 px-3 animate__animated animate__fadeInUp">
        <div class="col-md-8">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title"><i class="fas fa-chart-line me-2 text-primary"></i> PHÂN TÍCH HUẤN LUYỆN
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="min-height: 375px">
                        <canvas id="statisticsChart"></canvas>
                    </div>
                    <div id="myChartLegend"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-history me-2 text-danger"></i> HOẠT ĐỘNG GẦN ĐÂY</div>
                </div>
                <div class="card-body">
                    <ol class="activity-feed">
                        @forelse($activities as $activity)
                            <li class="feed-item">
                                @php
                                    $desc = $activity->description;
                                    $icon = 'fa-info-circle';
                                    $color = 'primary';
                                    if ($desc == 'created') {
                                        $desc = 'đã tạo mới';
                                        $icon = 'fa-plus-circle';
                                        $color = 'success';
                                    } elseif ($desc == 'updated') {
                                        $desc = 'đã cập nhật';
                                        $icon = 'fa-edit';
                                        $color = 'warning';
                                    } elseif ($desc == 'deleted') {
                                        $desc = 'đã xóa';
                                        $icon = 'fa-trash-alt';
                                        $color = 'danger';
                                    }

                                    $subjectMap = [
                                        'Soldier' => 'quân nhân',
                                        'Unit' => 'đơn vị',
                                        'WeaponEquipment' => 'vũ khí trang bị',
                                        'Reward' => 'khen thưởng',
                                        'Discipline' => 'kỷ luật',
                                        'TrainingResult' => 'kết quả tập huấn',
                                        'TrainingLog' => 'nhật ký huấn luyện',
                                        'User' => 'người dùng',
                                    ];
                                    $subjectName = class_basename($activity->subject_type);
                                    $friendlySubject = $subjectMap[$subjectName] ?? $subjectName;
                                @endphp
                                <div class="feed-item-icon bg-{{ $color }} shadow-sm">
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                <time class="date"
                                    datetime="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</time>
                                <span class="text">
                                    <strong>{{ $activity->causer->name ?? 'Hệ thống' }}</strong>
                                    {{ $desc }}
                                    <span class="text-{{ $color }} fw-bold">{{ $friendlySubject }}</span>
                                </span>
                            </li>
                        @empty
                            <li class="text-muted text-center py-4">Chưa có hoạt động nào</li>
                        @endforelse
                    </ol>
                    <div class="text-center mt-3">
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-link">Xem tất cả <i
                                class="fas fa-chevron-right ms-1"></i></a>
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
            let state = {
                level: '',
                levelLabel: '',
                unitPath: [], // Mảng các đơn vị đã chọn (id, name)
                unitId: '',
                unitName: '',
                moduleId: '',
                moduleLabel: '',
                moduleRoute: ''
            };

            const steps = ['#step-levels', '#step-units', '#step-modules', '#step-final'];

            function showStep(stepId, direction = 'next') {
                const currentStep = steps.find(s => !$(s).hasClass('d-none'));

                if (currentStep === stepId && stepId !== '#step-units') return;

                if (currentStep) {
                    const outAnim = direction === 'next' ? 'animate__fadeOutLeft' : 'animate__fadeOutRight';
                    const inAnim = direction === 'next' ? 'animate__fadeInRight' : 'animate__fadeInLeft';

                    if (currentStep === stepId) {
                        $(stepId).addClass('animate__fadeOut');
                        setTimeout(() => {
                            $(stepId).removeClass('animate__fadeOut').addClass('animate__fadeIn');
                        }, 300);
                    } else {
                        $(currentStep).addClass(outAnim);
                        setTimeout(() => {
                            $(currentStep).addClass('d-none').removeClass(outAnim);
                            $(stepId).removeClass('d-none').addClass(inAnim);
                            setTimeout(() => $(stepId).removeClass(inAnim), 1000);
                        }, 400);
                    }
                } else {
                    $(stepId).removeClass('d-none').addClass('animate__fadeIn');
                }

                updateBreadcrumb();
            }

            function updateBreadcrumb() {
                if (state.level) {
                    $('#navigation-breadcrumb').removeClass('d-none');
                    $('#breadcrumb-level').removeClass('d-none').html(
                        `<a href="#" class="breadcrumb-back" data-target="#step-levels">${state.levelLabel}</a>`
                        );
                } else {
                    $('#navigation-breadcrumb').addClass('d-none');
                    $('#breadcrumb-level').addClass('d-none');
                }

                if (state.unitPath.length > 0) {
                    let pathHtml = '';
                    state.unitPath.forEach((unit, index) => {
                        pathHtml += `<li class="breadcrumb-item"><a href="#" class="breadcrumb-unit-path" data-index="${index}">${unit.name}</a></li>`;
                    });
                    $('#breadcrumb-unit').removeClass('d-none').html(pathHtml);
                    $('#breadcrumb-unit').addClass('p-0 border-0 bg-transparent').css('display', 'contents');
                } else {
                    $('#breadcrumb-unit').addClass('d-none').removeClass('p-0 border-0 bg-transparent').css('display', '');
                }

                if (state.moduleId) {
                    $('#breadcrumb-module').removeClass('d-none').html(state.moduleLabel);
                } else {
                    $('#breadcrumb-module').addClass('d-none');
                }
            }

            function loadUnits(parentId = null) {
                $('#units-container').html('<div class="col-12 text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 fw-bold">Đang truy vấn dữ liệu đơn vị...</p></div>');

                const params = parentId ? { parent_id: parentId } : { level: state.level };

                $.get('{{ route("api.units-by-level") }}', params)
                .done(function(units) {
                    let html = '';
                    if (!units || units.length === 0) {
                        html = `
                            <div class="col-12 text-center text-muted py-5 animate__animated animate__fadeIn">
                                <i class="fas fa-folder-open fa-4x mb-3 opacity-25"></i>
                                <h5 class="fw-bold">Không còn đơn vị trực thuộc nào</h5>
                                <p>Bạn có thể xác nhận chọn đơn vị hiện tại để tiếp tục</p>
                                <button class="btn btn-primary btn-round btn-confirm-unit-direct mt-2 px-4 shadow">
                                    <i class="fas fa-check me-2"></i> Xác nhận chọn đơn vị này
                                </button>
                            </div>`;
                    } else {
                        units.forEach(unit => {
                            const hasChildren = unit.children_count > 0;
                            html += `
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="unit-card-wrapper animate__animated animate__zoomIn">
                                        <div class="unit-card shadow-sm border-0 mb-3 overflow-hidden" data-id="${unit.id}" data-name="${unit.name}">
                                            <div class="card-unit-inner p-0 text-center">
                                                <div class="unit-icon-box py-4">
                                                    <div class="icon-circle-main mx-auto shadow-sm">
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                </div>
                                                <div class="unit-info-box px-3 pb-3">
                                                    <h6 class="fw-extrabold text-dark mb-3 text-uppercase text-truncate-2" title="${unit.name}">${unit.name}</h6>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-primary btn-sm flex-fill btn-select-unit fw-bold" data-id="${unit.id}" data-name="${unit.name}">
                                                            CHỌN
                                                        </button>
                                                        ${hasChildren ? `
                                                            <button class="btn btn-outline-info btn-sm flex-fill btn-drill-unit" data-id="${unit.id}" data-name="${unit.name}" title="Xem đơn vị trực thuộc">
                                                                <i class="fas fa-chevron-right"></i>
                                                            </button>
                                                        ` : ''}
                                                    </div>
                                                </div>
                                                ${hasChildren ? `<div class="unit-badge-children small">${unit.children_count} Đơn vị trực thuộc</div>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $('#units-container').html(html);
                })
                .fail(function() {
                    $('#units-container').html('<div class="col-12 text-center text-danger py-5"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><br>Lỗi kết nối máy chủ. Vui lòng thử lại.</div>');
                });
            }

            // --- Event Handlers ---

            $('.level-btn').on('click', function() {
                state.level = $(this).data('level');
                state.levelLabel = $(this).find('span').text();
                state.unitPath = [];
                state.unitId = '';
                state.moduleId = '';

                $('#selected-level-label-units').text(state.levelLabel);
                showStep('#step-units');
                loadUnits();
            });

            $(document).on('click', '.btn-drill-unit', function(e) {
                e.stopPropagation();
                const id = $(this).data('id');
                const name = $(this).data('name');
                state.unitPath.push({ id, name });
                updateBreadcrumb();
                loadUnits(id);
            });

            $(document).on('click', '.btn-select-unit, .btn-confirm-unit-direct', function(e) {
                e.stopPropagation();
                if ($(this).hasClass('btn-confirm-unit-direct')) {
                    const lastUnit = state.unitPath[state.unitPath.length - 1];
                    state.unitId = lastUnit.id;
                    state.unitName = lastUnit.name;
                } else {
                    state.unitId = $(this).data('id');
                    state.unitName = $(this).data('name');
                }

                state.moduleId = '';
                $('#selected-unit-label').text(state.unitName);
                showStep('#step-modules');
            });

            $(document).on('click', '.breadcrumb-unit-path', function(e) {
                e.preventDefault();
                const index = $(this).data('index');
                const unit = state.unitPath[index];
                state.unitPath = state.unitPath.slice(0, index + 1);
                updateBreadcrumb();
                loadUnits(unit.id);
            });

            $(document).on('click', '.breadcrumb-back, #breadcrumb-home, #btn-reset-wizard', function(e) {
                e.preventDefault();
                const target = $(this).data('target') || '#step-levels';

                if (target === '#step-levels') {
                    state.level = ''; state.unitPath = []; state.unitId = ''; state.moduleId = '';
                } else if (target === '#step-units') {
                    state.unitPath = []; state.unitId = ''; state.moduleId = '';
                    loadUnits();
                }

                showStep(target, 'prev');
            });

            $('.module-btn').on('click', function() {
                state.moduleId = $(this).data('id');
                state.moduleLabel = $(this).data('label');
                state.moduleRoute = $(this).data('route');
                const color = $(this).css('background');

                $('#selected-module-label').text(state.moduleLabel);

                // Cập nhật Step 4 Content
                $('#final-all-card').css('background', color);
                $('#final-all-link').data('route', state.moduleRoute);

                let searchModule = state.moduleId;
                if (searchModule === 'weapons') searchModule = 'weapon_equipments';
                if (searchModule === 'training') searchModule = 'training_results';
                if (searchModule === 'logs') searchModule = 'training_logs';

                const searchRoute = '{{ route('search.index') }}?module=' + searchModule;
                $('#final-search-link').data('route', searchRoute);

                // Tùy chỉnh mô tả
                $('#final-all-desc').text('Hiển thị danh sách ' + state.moduleLabel.toLowerCase() +
                    ' của ' + state.unitName);
                $('#final-search-desc').text('Tìm kiếm quân nhân, hồ sơ theo tiêu chí nâng cao');

                showStep('#step-final');
            });

            $('.final-option-link').on('click', function(e) {
                e.preventDefault();
                const baseUrl = $(this).data('route');
                const finalUrl = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'level=' + state.level +
                    '&unit_id=' + state.unitId;

                // Hiển thị hiệu ứng chuyển trang mượt mà
                $('body').addClass('animate__animated animate__fadeOut');
                setTimeout(() => {
                    window.location.href = finalUrl;
                }, 300);
            });

            $('#btn-refresh-dashboard').on('click', function() {
                $(this).find('i').addClass('fa-spin');
                setTimeout(() => {
                    location.reload();
                }, 500);
            });

            // --- Charts Implementation ---

            // 1. Phân tích huấn luyện (stacked counts by result per month + avg passing rate line)
            const ctx = document.getElementById('statisticsChart').getContext('2d');
            // Use Chart.js v2-compatible config (most bundled themes use v2)
            const statisticsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
                    datasets: [
                        {
                            label: 'Xuất sắc',
                            data: [
                                @for($m=1;$m<=12;$m++) {{ $statsByMonth[$m]['by_result']['xuất_sắc'] }}, @endfor
                            ],
                            backgroundColor: '#28a745'
                        },
                        {
                            label: 'Giỏi',
                            data: [
                                @for($m=1;$m<=12;$m++) {{ $statsByMonth[$m]['by_result']['giỏi'] }}, @endfor
                            ],
                            backgroundColor: '#007bff'
                        },
                        {
                            label: 'Khá',
                            data: [
                                @for($m=1;$m<=12;$m++) {{ $statsByMonth[$m]['by_result']['khá'] }}, @endfor
                            ],
                            backgroundColor: '#17a2b8'
                        },
                        {
                            label: 'Trung bình',
                            data: [
                                @for($m=1;$m<=12;$m++) {{ $statsByMonth[$m]['by_result']['trung_bình'] }}, @endfor
                            ],
                            backgroundColor: '#ffc107'
                        },
                        {
                            label: 'Yếu',
                            data: [
                                @for($m=1;$m<=12;$m++) {{ $statsByMonth[$m]['by_result']['yếu'] }}, @endfor
                            ],
                            backgroundColor: '#dc3545'
                        },
                        {
                            label: 'Tỉ lệ đạt trung bình (%)',
                            type: 'line',
                            data: [
                                @for($m=1;$m<=12;$m++) {{ round($statsByMonth[$m]['avg_passing_rate'],2) }}, @endfor
                            ],
                            borderColor: '#343a40',
                            backgroundColor: '#343a40',
                            fill: false,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        xAxes: [{
                            stacked: true
                        }],
                        yAxes: [{
                            stacked: true,
                            ticks: { beginAtZero: true }
                        }, {
                            id: 'y1',
                            position: 'right',
                            ticks: {
                                callback: function(value) { return value + '%'; },
                                beginAtZero: true
                            },
                            gridLines: { display: false }
                        }]
                    }
                }
            });

            // 2. Training Results Chart (Doughnut)
            const ctx2 = document.getElementById('trainingResultChart').getContext('2d');
            const trainingResultChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: {!! json_encode($resultData) !!},
                        backgroundColor: ['#1d7af3', '#59d05d', '#ffad46', '#f3545d', '#8d9498']
                    }],
                    labels: {!! json_encode($resultLabels) !!}
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 70
                }
            });
        });
    </script>

    <style>
        .breadcrumb-style1 {
            background: white;
            border-radius: 50px;
            font-weight: 600;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "\f105";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            color: #b9bbbe;
        }

        .unit-card {
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            border-radius: 20px;
            background: #fff;
            position: relative;
        }

        .unit-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
        }

        .unit-icon-box {
            background: linear-gradient(to bottom, rgba(21, 114, 232, 0.03) 0%, rgba(21, 114, 232, 0) 100%);
        }

        .icon-circle-main {
            width: 70px;
            height: 70px;
            background: #fff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #1572e8;
            transition: all 0.3s ease;
        }

        .unit-card:hover .icon-circle-main {
            background: var(--primary-gradient);
            color: white;
            transform: scale(1.1) rotate(5deg);
        }

        .unit-badge-children {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(21, 114, 232, 0.1);
            color: #1572e8;
            padding: 2px 10px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 10px;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.8em;
            line-height: 1.4;
        }

        .breadcrumb-unit-path {
            color: #1572e8 !important;
            font-weight: 700;
        }

        .breadcrumb-unit-path:hover {
            text-decoration: underline !important;
        }

        .btn-floating-nav {
            border: none;
            border-radius: 24px;
            padding: 25px 20px;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            height: 100%;
            min-height: 200px;
        }

        .btn-floating-nav:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.2);
        }

        .btn-floating-nav .icon-wrap {
            background: rgba(255, 255, 255, 0.2);
            width: 85px;
            height: 85px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 24px;
            font-size: 3rem;
            transition: all 0.5s ease;
        }

        .btn-floating-nav:hover .icon-wrap {
            background: rgba(255, 255, 255, 0.35);
            transform: rotate(10deg);
        }

        .btn-floating-nav span {
            font-weight: 800;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Activity Feed Styles */
        .activity-feed {
            padding: 0;
            list-style: none;
        }

        .feed-item {
            position: relative;
            padding-bottom: 25px;
            padding-left: 45px;
            border-left: 2px solid #e4e8eb;
        }

        .feed-item:last-child {
            border-color: transparent;
        }

        .feed-item-icon {
            position: absolute;
            top: 0;
            left: -15px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            z-index: 1;
        }

        .feed-item .date {
            display: block;
            position: relative;
            top: -5px;
            color: #8d9498;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .feed-item .text {
            position: relative;
            top: -3px;
            font-size: 14px;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .btn-floating-nav {
                height: 160px !important;
            }

            .btn-floating-nav .icon-wrap {
                width: 60px;
                height: 60px;
                font-size: 2rem !important;
            }
        }
    </style>
@endsection
