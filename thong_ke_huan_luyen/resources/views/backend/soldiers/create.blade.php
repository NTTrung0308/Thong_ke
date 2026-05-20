@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Thêm quân nhân mới</h3>
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
                    <div class="card-title">Thông tin quân nhân</div>
                </div>
                <form action="{{ route('soldiers.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="code">Số hiệu quân nhân <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="full_name">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="d-block mb-2">Đơn vị biên chế <span class="text-danger">*</span></label>
                                    <div class="row g-2">
                                        @php
                                            $levelMap = [
                                                'chi-huy' => ['label' => 'Bộ chỉ huy', 'id' => 'unit_chi_huy'],
                                                'trung-doan' => ['label' => 'Trung đoàn', 'id' => 'unit_trung_doan'],
                                                'tieu-doan' => ['label' => 'Tiểu đoàn', 'id' => 'unit_tieu_doan'],
                                                'dai-doi' => ['label' => 'Đại đội', 'id' => 'unit_dai_doi'],
                                                'trung-doi' => ['label' => 'Trung đội', 'id' => 'unit_trung_doi'],
                                            ];
                                            $levelKeys = array_keys($levelMap);
                                        @endphp

                                        @foreach($levelKeys as $index => $levelKey)
                                            <div class="col-md">
                                                <label class="small text-muted">{{ $index + 1 }}. {{ $levelMap[$levelKey]['label'] }}</label>
                                                <select class="form-select form-control unit-selector" data-level="{{ $levelKey }}" id="{{ $levelMap[$levelKey]['id'] }}" {{ !isset($levelOptions[$index]) ? 'disabled' : '' }}>
                                                    <option value="">-- Chọn {{ $levelMap[$levelKey]['label'] }} --</option>
                                                    @if(isset($levelOptions[$index]))
                                                        @foreach($levelOptions[$index] as $option)
                                                            <option value="{{ $option->id }}" {{ (isset($hierarchy[$index]) && $hierarchy[$index]->id == $option->id) ? 'selected' : '' }}>
                                                                {{ $option->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="unit_id" id="final_unit_id" value="{{ old('unit_id') }}" required>
                                    @error('unit_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rank">Cấp bậc <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('rank') is-invalid @enderror" id="rank" name="rank" value="{{ old('rank') }}" required placeholder="Ví dụ: Binh nhì, Hạ sĩ...">
                                    @error('rank')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="position">Chức vụ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position') }}" required placeholder="Ví dụ: Chiến sĩ, Tiểu đội trưởng...">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="birth_date">Ngày sinh <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
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
                                    <input type="date" class="form-control @error('enlistment_date') is-invalid @enderror" id="enlistment_date" name="enlistment_date" value="{{ old('enlistment_date') }}" required>
                                    @error('enlistment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="party_join_date">Ngày vào Đảng/Đoàn</label>
                                    <input type="date" class="form-control @error('party_join_date') is-invalid @enderror" id="party_join_date" name="party_join_date" value="{{ old('party_join_date') }}">
                                    @error('party_join_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="education">Trình độ văn hóa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('education') is-invalid @enderror" id="education" name="education" value="{{ old('education') }}" required placeholder="Ví dụ: 12/12, Đại học...">
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
                                    <input type="text" class="form-control @error('foreign_language') is-invalid @enderror" id="foreign_language" name="foreign_language" value="{{ old('foreign_language') }} " required placeholder="Ví dụ: Tiếng Anh - Trung cấp...">
                                    @error('foreign_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="professional_level">Nghể nghiệp bậc chuyên môn</label>
                                    <input type="text" class="form-control @error('professional_level') is-invalid @enderror" id="professional_level" name="professional_level" value="{{ old('professional_level') }}">
                                    @error('professional_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="permanent_residence">Hộ khẩu thường trú <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('permanent_residence') is-invalid @enderror" id="permanent_residence" name="permanent_residence" rows="2" required>{{ old('permanent_residence') }}</textarea>
                            @error('permanent_residence')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_name">Người báo tin khi cần <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required>
                                    @error('emergency_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_address">Địa chỉ người báo tin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('emergency_contact_address') is-invalid @enderror" id="emergency_contact_address" name="emergency_contact_address" value="{{ old('emergency_contact_address') }}" required>
                                    @error('emergency_contact_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="notes">Ghi chú</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Lưu lại</button>
                        <a href="{{ route('soldiers.index') }}" class="btn btn-danger">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];

    function updateFinalUnitId() {
        let lastId = '';
        levels.forEach(level => {
            const val = $(`#unit_${level.replace('-', '_')}`).val();
            if (val) lastId = val;
        });
        $('#final_unit_id').val(lastId);
    }

    $('.unit-selector').on('change', function() {
        const parentId = $(this).val();
        const currentLevel = $(this).data('level');
        const currentIndex = levels.indexOf(currentLevel);

        // Reset all lower levels
        for (let i = currentIndex + 1; i < levels.length; i++) {
            const $nextSelect = $(`#unit_${levels[i].replace('-', '_')}`);
            $nextSelect.html(`<option value="">-- Chọn ${$nextSelect.prev('label').text().split('. ')[1]} --</option>`);
            $nextSelect.prop('disabled', true);
        }

        updateFinalUnitId();

        if (parentId && currentIndex < levels.length - 1) {
            const nextLevel = levels[currentIndex + 1];
            const $nextSelect = $(`#unit_${nextLevel.replace('-', '_')}`);

            $.ajax({
                url: `{{ route('units.getChildren', '') }}/${parentId}`,
                type: 'GET',
                success: function(data) {
                    if (data.length > 0) {
                        let options = `<option value="">-- Chọn ${$nextSelect.prev('label').text().split('. ')[1]} --</option>`;
                        data.forEach(unit => {
                            options += `<option value="${unit.id}">${unit.name}</option>`;
                        });
                        $nextSelect.html(options);
                        $nextSelect.prop('disabled', false);
                    }
                }
            });
        }
    });
});
</script>
@endsection
