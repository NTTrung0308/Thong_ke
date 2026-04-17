@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chỉnh sửa thông tin quân nhân</h3>
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
                <a href="{{ route('soldiers.index') }}">Quân nhân</a>
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
                    <div class="card-title">Cập nhật thông tin cho: {{ $soldier->full_name }}</div>
                </div>
                <form action="{{ route('soldiers.update', $soldier->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="code">Số hiệu quân nhân <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $soldier->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="full_name">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name', $soldier->full_name) }}" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unit_id">Đơn vị <span class="text-danger">*</span></label>
                                    <select class="form-select form-control @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                        <option value="">-- Chọn đơn vị --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id', $soldier->unit_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rank">Cấp bậc <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('rank') is-invalid @enderror" id="rank" name="rank" value="{{ old('rank', $soldier->rank) }}" required>
                                    @error('rank')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="position">Chức vụ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $soldier->position) }}" required>
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="birth_date">Ngày sinh <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $soldier->birth_date) }}" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="enlistment_date">Ngày nhập ngũ <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('enlistment_date') is-invalid @enderror" id="enlistment_date" name="enlistment_date" value="{{ old('enlistment_date', $soldier->enlistment_date) }}" required>
                                    @error('enlistment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="party_join_date">Ngày vào Đảng/Đoàn</label>
                                    <input type="date" class="form-control @error('party_join_date') is-invalid @enderror" id="party_join_date" name="party_join_date" value="{{ old('party_join_date', $soldier->party_join_date) }}">
                                    @error('party_join_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="education">Trình độ văn hóa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('education') is-invalid @enderror" id="education" name="education" value="{{ old('education', $soldier->education) }}" required>
                                    @error('education')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="foreign_language">Ngoại ngữ</label>
                                    <input type="text" class="form-control @error('foreign_language') is-invalid @enderror" id="foreign_language" name="foreign_language" value="{{ old('foreign_language', $soldier->foreign_language) }}">
                                    @error('foreign_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="professional_level">Trình độ chuyên môn</label>
                                    <input type="text" class="form-control @error('professional_level') is-invalid @enderror" id="professional_level" name="professional_level" value="{{ old('professional_level', $soldier->professional_level) }}">
                                    @error('professional_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="permanent_residence">Hộ khẩu thường trú <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('permanent_residence') is-invalid @enderror" id="permanent_residence" name="permanent_residence" rows="2" required>{{ old('permanent_residence', $soldier->permanent_residence) }}</textarea>
                            @error('permanent_residence')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_name">Người báo tin khi cần <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $soldier->emergency_contact_name) }}" required>
                                    @error('emergency_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_address">Địa chỉ người báo tin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('emergency_contact_address') is-invalid @enderror" id="emergency_contact_address" name="emergency_contact_address" value="{{ old('emergency_contact_address', $soldier->emergency_contact_address) }}" required>
                                    @error('emergency_contact_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="notes">Ghi chú</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $soldier->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                        <a href="{{ route('soldiers.index') }}" class="btn btn-danger">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
