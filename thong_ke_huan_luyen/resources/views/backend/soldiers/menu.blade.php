@extends('backend.layouts.dashboard')

@section('dashboard_content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Quản lý Quân nhân</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="fas fa-chevron-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('soldiers.menu') }}">Quân nhân</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Chọn hình thức hiển thị</h4>
            </div>
            <div class="card-body">
                <div class="row justify-content-center py-5">
                    <div class="col-md-5 mb-4">
                        <a href="{{ route('soldiers.index', ['level' => $level]) }}" class="card card-stats card-round text-decoration-none hover-effect">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <h4 class="card-title text-primary fw-bold">Xem toàn bộ</h4>
                                            <p class="card-category">Hiển thị danh sách quân nhân theo đơn vị</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-5 mb-4">
                        <a href="{{ route('search.index', ['level' => $level]) }}" class="card card-stats card-round text-decoration-none hover-effect">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-info bubble-shadow-small">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <h4 class="card-title text-info fw-bold">Tìm kiếm</h4>
                                            <p class="card-category">Tra cứu quân nhân nâng cao và kiểm tra thông tin</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-effect {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-color: #1572e8;
    }
    .hover-effect:hover .text-info {
        color: #1d7af3 !important;
    }
    .card-stats .numbers .card-title {
        margin-bottom: 5px;
        font-size: 1.5rem;
    }
    .col-icon {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .icon-big i {
        font-size: 3rem;
    }
</style>
@endsection
