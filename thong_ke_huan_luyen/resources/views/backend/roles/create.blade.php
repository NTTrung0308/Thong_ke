@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Thêm vai trò mới</h3>
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
                <a href="{{ route('roles.index') }}">Vai trò & Quyền</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Thêm mới</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Thông tin vai trò</div>
                </div>
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @php
                            $labels = [
                                'view-unit' => 'Xem đơn vị',
                                'manage-unit' => 'Quản lý đơn vị',
                                'create-unit' => 'Thêm đơn vị',
                                'delete-unit' => 'Xóa đơn vị',
                                'view-report' => 'Xem báo cáo',
                                'create-report' => 'Tạo báo cáo',
                                'approve-report' => 'Phê duyệt báo cáo',
                                'export-report' => 'Xuất báo cáo',
                                'view-personnel' => 'Xem quân nhân',
                                'manage-personnel' => 'Quản lý quân nhân',
                                'assign-personnel' => 'Điều động quân nhân',
                                'manage-roles' => 'Quản lý vai trò/quyền',
                                'system-settings' => 'Cài đặt hệ thống',
                            ];
                        @endphp
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Tên vai trò (Ví dụ: chi-huy, dai-doi-truong...) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="d-block">Gán các quyền truy cập</label>
                                    <div class="row mt-2">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                        <strong>{{ $permission->name }}</strong>
                                                        <span class="text-muted">({{ $labels[$permission->name] ?? 'Chưa có mô tả' }})</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-action text-center">
                        <button type="submit" class="btn btn-success">Lưu vai trò</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-danger">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
