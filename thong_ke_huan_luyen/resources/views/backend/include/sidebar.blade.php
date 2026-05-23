<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('dashboard') }}" class="logo">
                {{-- <img src="{{ asset('backend/assets/img/kaiadmin/logo_light.svg') }}" alt="navbar brand"
                    class="navbar-brand" height="20" /> --}}
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item {{ Request::is('dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i>
                        <p>Trang chủ</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('search*') ? 'active' : '' }}">
                    <a href="{{ route('search.index') }}">
                        <i class="fas fa-search"></i>
                        <p>Kiểm tra (Tìm kiếm)</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Quản lý Nghiệp vụ</h4>
                </li>
                <li class="nav-item {{ Request::is('soldiers*') ? 'active' : '' }}">
                    <a href="{{ route('soldiers.index') }}">
                        <i class="fas fa-users"></i>
                        <p>Danh sách quân nhân</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('weapon-equipments*') ? 'active' : '' }}">
                    <a href="{{ route('weapon-equipments.index') }}">
                        <i class="fas fa-shield-alt"></i>
                        <p>Vũ khí trang bị</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('rewards*') ? 'active' : '' }}">
                    <a href="{{ route('rewards.index') }}">
                        <i class="fas fa-award"></i>
                        <p>Khen thưởng</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('disciplines*') ? 'active' : '' }}">
                    <a href="{{ route('disciplines.index') }}">
                        <i class="fas fa-gavel"></i>
                        <p>Kỷ luật</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('training-results*') ? 'active' : '' }}">
                    <a href="{{ route('training-results.index') }}">
                        <i class="fas fa-graduation-cap"></i>
                        <p>Kết quả tập huấn</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('training-logs*') ? 'active' : '' }}">
                    <a href="{{ route('training-logs.index') }}">
                        <i class="fas fa-book"></i>
                        <p>Nhật ký huấn luyện</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('training-subjects*') ? 'active' : '' }}">
                    <a href="{{ route('training-subjects.index') }}">
                        <i class="fas fa-list-alt"></i>
                        <p>Nội dung huấn luyện</p>
                    </a>
                </li>
                @if (auth()->user()->hasRole('chi-huy'))
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Cài đặt hệ thống</h4>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#settings">
                            <i class="fas fa-cog"></i>
                            <p>Hệ thống</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="settings">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('units.index') }}">
                                        <span class="sub-item">Quản lý đơn vị</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('training-subjects.index') }}">
                                        <span class="sub-item">Nội dung huấn luyện</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('users.index') }}">
                                        <span class="sub-item">Quản lý người dùng</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('activity-logs.index') }}">
                                        <span class="sub-item">Nhật ký hoạt động</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="sub-item">Vai trò & Quyền</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->
