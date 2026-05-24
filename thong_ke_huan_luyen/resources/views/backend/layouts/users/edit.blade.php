@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chỉnh sửa tài khoản</h3>
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
                <a href="{{ route('users.index') }}">Tài khoản</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Chỉnh sửa</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Cập nhật thông tin: {{ $user->name }}</div>
                </div>
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group @error('name') has-error @enderror">
                                    <label for="name">Họ và tên</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Nhập họ tên" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group @error('email') has-error @enderror">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Nhập email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group @error('unit_id') has-error @enderror">
                                    <label for="unit_id">Đơn vị</label>
                                    <select class="form-control" id="unit_id" name="unit_id">
                                        <option value="">-- Chọn đơn vị --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ (old('unit_id', $user->unit_id) == $unit->id) ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group @error('soldier_id') has-error @enderror">
                                    <label for="soldier_id">Quân nhân liên kết (Nếu có)</label>
                                    <select class="form-control select2" id="soldier_id" name="soldier_id">
                                        <option value="">-- Chọn quân nhân --</option>
                                        @foreach($soldiers as $soldier)
                                            <option value="{{ $soldier->id }}" data-unit-id="{{ $soldier->unit_id }}" {{ (old('soldier_id', $user->soldier_id) == $soldier->id) ? 'selected' : '' }}>
                                                {{ $soldier->full_name }} ({{ $soldier->unit->name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('soldier_id')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                </div>
                            <div class="col-md-6">
                                <div class="form-group @error('password') has-error @enderror">
                                    <label for="password">Mật khẩu mới (để trống nếu không đổi)</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Mật khẩu mới">
                                    @error('password')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Xác nhận mật khẩu mới">
                                </div>
                                <div class="mt-2">
                                    <button type="button" id="togglePasswordUserEdit" class="btn btn-sm btn-outline-secondary">
                                        <svg id="eyeIconEdit" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span class="ms-1">Hiển thị</span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Vai trò (Phân quyền)</label>
                                    <div class="d-flex flex-wrap">
                                        @foreach ($roles as $role)
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="checkbox" name="roles[]"
                                                    value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                    {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('roles')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                        <a href="{{ route('users.index') }}" class="btn btn-danger">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($('.select2').length > 0) {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
            }

            $('#soldier_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const unitId = selectedOption.data('unit-id');
                if (unitId) {
                    $('#unit_id').val(unitId);
                }
            });
        });

        (function(){
            var btn = document.getElementById('togglePasswordUserEdit');
            var pwd = document.getElementById('password');
            var pwdc = document.getElementById('password_confirmation');
            var eye = document.getElementById('eyeIconEdit');
            if(btn){
                btn.addEventListener('click', function(){
                    var type = (pwd && pwd.type === 'password') ? 'text' : 'password';
                    if(pwd) pwd.type = type;
                    if(pwdc) pwdc.type = type;
                    if(eye){
                        if(type === 'text') eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>';
                        else eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
                    }
                });
            }
        })();
    </script>
@endsection
