@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Thêm Đơn vị mới</h3>
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
                <a href="#">Thêm mới</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Thông tin đơn vị</div>
                </div>
                <form action="{{ route('units.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Tên đơn vị <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ old('name') }}" placeholder="Nhập tên đơn vị (VD: Đại đội 1)" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="level">Cấp đơn vị <span class="text-danger">*</span></label>
                            <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                <option value="">-- Chọn cấp --</option>
                                <option value="chi-huy" {{ old('level') == 'chi-huy' ? 'selected' : '' }}>Cấp Chỉ huy</option>
                                <option value="trung-doan" {{ old('level') == 'trung-doan' ? 'selected' : '' }}>Cấp Trung đoàn</option>
                                <option value="tieu-doan" {{ old('level') == 'tieu-doan' ? 'selected' : '' }}>Cấp Tiểu đoàn</option>
                                <option value="dai-doi" {{ old('level') == 'dai-doi' ? 'selected' : '' }}>Cấp Đại đội</option>
                                <option value="trung-doi" {{ old('level') == 'trung-doi' ? 'selected' : '' }}>Cấp Trung đội</option>
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
                                    <option value="{{ $pUnit->id }}" {{ old('parent_id') == $pUnit->id ? 'selected' : '' }}>
                                        {{ $pUnit->name }} ({{ $pUnit->level_label }})
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Lưu đơn vị</button>
                        <a href="{{ route('units.index') }}" class="btn btn-danger">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
