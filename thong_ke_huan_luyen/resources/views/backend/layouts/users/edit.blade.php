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
                                            <option value="{{ $unit->id }}" {{ old('unit_id', $user->unit_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
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
                                <label class="inline-flex items-center mt-2">
                                    <input type="checkbox" id="togglePasswordUserEdit" class="form-check-input me-2">
                                    <span class="ml-2">Hiển thị mật khẩu</span>
                                </label>
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

    <script>
        (function(){
            var toggle = document.getElementById('togglePasswordUserEdit');
            var pwd = document.getElementById('password');
            var pwdc = document.getElementById('password_confirmation');
            if(toggle){
                toggle.addEventListener('change', function(){
                    var type = this.checked ? 'text' : 'password';
                    if(pwd) pwd.type = type;
                    if(pwdc) pwdc.type = type;
                });
            }
        })();
    </script>
@endsection
