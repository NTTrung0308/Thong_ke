@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Quản lý Vai trò (Roles)</h3>
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
                <a href="#">Hệ thống</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Vai trò & Quyền</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Danh sách Vai trò</h4>
                        <div class="ms-auto">
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary btn-round me-2">
                                <i class="fa fa-key"></i>
                                Quản lý Quyền hạn
                            </a>
                            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-round">
                                <i class="fa fa-plus"></i>
                                Thêm vai trò mới
                            </a>
                        </div>
                    </div>
                </div>
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
                    <div class="table-responsive">
                        <table id="basic-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tên vai trò</th>
                                    <th>Các quyền được gán</th>
                                    <th style="width: 10%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>
                                            <strong>{{ $role->name }}</strong>
                                        </td>
                                        <td>
                                            @foreach($role->permissions as $permission)
                                                <span class="badge badge-info mb-1" title="{{ $permission->name }}">
                                                    {{ $labels[$permission->name] ?? $permission->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('roles.edit', $role->id) }}"
                                                    class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip"
                                                    title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @if($role->name !== 'chi-huy')
                                                <form action="{{ route('roles.destroy', $role->id) }}"
                                                    method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link btn-danger"
                                                        data-bs-toggle="tooltip" title="Xóa"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
