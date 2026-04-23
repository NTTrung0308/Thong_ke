@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chỉnh sửa Đơn vị</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="fas fa-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('units.index') }}">Đơn vị</a>
            </li>
            <li class="separator">
                <i class="fas fa-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Chỉnh sửa</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Thông tin đơn vị: {{ $unit->name }}</div>
                </div>
                <form action="{{ route('units.update', $unit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Tên đơn vị <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ old('name', $unit->name) }}" placeholder="Nhập tên đơn vị" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="level">Cấp đơn vị <span class="text-danger">*</span></label>
                            <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                <option value="">-- Chọn cấp --</option>
                                <option value="1" {{ old('level', $unit->level) == 1 ? 'selected' : '' }}>Cấp 1 (Lữ đoàn/Trung đoàn)</option>
                                <option value="2" {{ old('level', $unit->level) == 2 ? 'selected' : '' }}>Cấp 2 (Tiểu đoàn)</option>
                                <option value="3" {{ old('level', $unit->level) == 3 ? 'selected' : '' }}>Cấp 3 (Đại đội)</option>
                                <option value="4" {{ old('level', $unit->level) == 4 ? 'selected' : '' }}>Cấp 4 (Trung đội)</option>
                                <option value="5" {{ old('level', $unit->level) == 5 ? 'selected' : '' }}>Cấp 5 (Tiểu đội)</option>
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="parent_id">Đơn vị trực thuộc</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                <option value="">-- Không có (Cấp cao nhất) --</option>
                                @foreach ($parentUnits as $pUnit)
                                    <option value="{{ $pUnit->id }}" {{ old('parent_id', $unit->parent_id) == $pUnit->id ? 'selected' : '' }}>
                                        {{ $pUnit->name }} (Cấp {{ $pUnit->level }})
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Lưu ý: Không thể chọn chính đơn vị này làm đơn vị trực thuộc.</small>
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                        <a href="{{ route('units.index') }}" class="btn btn-danger">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
