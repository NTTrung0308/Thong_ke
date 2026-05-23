@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Sửa nội dung huấn luyện</h3>
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
                <a href="{{ route('training-subjects.index') }}">Nội dung huấn luyện</a>
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
                    <div class="card-title">Thông tin nội dung: {{ $trainingSubject->name }}</div>
                </div>
                <form action="{{ route('training-subjects.update', $trainingSubject->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Tên nội dung <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $trainingSubject->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit_level">Áp dụng cho cấp <span class="text-danger">*</span></label>
                                    <select class="form-select @error('unit_level') is-invalid @enderror" id="unit_level" name="unit_level" required>
                                        <option value="dai-doi" {{ old('unit_level', $trainingSubject->unit_level) == 'dai-doi' ? 'selected' : '' }}>Đại đội</option>
                                        <option value="trung-doi" {{ old('unit_level', $trainingSubject->unit_level) == 'trung-doi' ? 'selected' : '' }}>Trung đội</option>
                                    </select>
                                    @error('unit_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="parent_id">Thuộc nội dung</label>
                                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                        <option value="">-- Là Môn học gốc --</option>
                                        @foreach($subjects as $s)
                                            <option value="{{ $s->id }}" {{ old('parent_id', $trainingSubject->parent_id) == $s->id ? 'selected' : '' }}>
                                                {{ $s->unit_level == 'dai-doi' ? '[ĐĐ]' : '[TĐ]' }} {{ $s->getFullPathAttribute() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                        <a href="{{ route('training-subjects.index') }}" class="btn btn-danger">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
