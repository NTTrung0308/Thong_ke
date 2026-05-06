@extends('backend.layouts.dashboard')

@section('dashboard_content')
<div class="row mt-4">
    <div class="col-md-12 text-center">
        <h2 class="fw-bold mb-4">HỆ THỐNG THỐNG KÊ HUẤN LUYỆN CHIẾN ĐẤU</h2>
    </div>
</div>

<div class="row justify-content-center">
    <!-- Nút Thống kê huấn luyện -->
    <div class="col-md-8">
        <div class="card card-stats card-round p-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Chức năng chính</p>
                            <h4 class="card-title">Thống kê huấn luyện</h4>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#unitLevels" aria-expanded="false">
                            <i class="fas fa-chevron-down"></i> Hiển thị thao tác
                        </button>
                    </div>
                </div>

                <div class="collapse mt-4" id="unitLevels">
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        @php
                            $userLevel = auth()->user()->unit ? auth()->user()->unit->level : 'trung-doan';
                            $levels = [
                                'trung-doan' => 'Cấp Trung đoàn',
                                'tieu-doan' => 'Cấp Tiểu đoàn',
                                'dai-doi' => 'Cấp Đại đội',
                                'trung-doi' => 'Cấp Trung đội'
                            ];
                            $show = false;
                            if(auth()->user()->hasRole('chi-huy')) $show = true;
                        @endphp

                        @foreach($levels as $key => $label)
                            @if($show || $userLevel == $key || $show = ($userLevel == $key ? true : $show))
                                <a href="{{ route('soldiers.index', ['level' => $key]) }}" class="btn btn-outline-primary btn-lg px-4 py-3">
                                    <i class="fas fa-layer-group mb-2"></i><br>
                                    {{ $label }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nút Kiểm tra -->
    <div class="col-md-8 mt-4">
        <div class="card card-stats card-round p-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Tra cứu nhanh</p>
                            <h4 class="card-title">Kiểm tra</h4>
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('search.index') }}" class="btn btn-info text-white">
                            <i class="fas fa-search"></i> Truy cập tìm kiếm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-stats:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .btn-lg {
        min-width: 150px;
    }
</style>
@endsection
