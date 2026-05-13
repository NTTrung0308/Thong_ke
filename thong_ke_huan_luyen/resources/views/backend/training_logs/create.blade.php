@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Thêm nhật ký huấn luyện</h3>
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
                <a href="{{ route('training-logs.index') }}">Nhật ký huấn luyện</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Thêm mới</a>
            </li>
        </ul>
    </div>

    <form action="{{ route('training-logs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Thông tin đơn vị và thời gian -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Thông tin chung</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="d-block mb-2">Đơn vị huấn luyện <span class="text-danger">*</span></label>
                                    <div class="row g-2">
                                        <div class="col-md">
                                            <label class="small text-muted">1. Bộ chỉ huy</label>
                                            <select class="form-select form-control unit-selector" data-level="chi-huy" id="unit_chi_huy">
                                                <option value="">-- Chọn Bộ chỉ huy --</option>
                                                @foreach($rootUnits as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md">
                                            <label class="small text-muted">2. Trung đoàn</label>
                                            <select class="form-select form-control unit-selector" data-level="trung-doan" id="unit_trung_doan" disabled>
                                                <option value="">-- Chọn Trung đoàn --</option>
                                            </select>
                                        </div>
                                        <div class="col-md">
                                            <label class="small text-muted">3. Tiểu đoàn</label>
                                            <select class="form-select form-control unit-selector" data-level="tieu-doan" id="unit_tieu_doan" disabled>
                                                <option value="">-- Chọn Tiểu đoàn --</option>
                                            </select>
                                        </div>
                                        <div class="col-md">
                                            <label class="small text-muted">4. Đại đội</label>
                                            <select class="form-select form-control unit-selector" data-level="dai-doi" id="unit_dai_doi" disabled>
                                                <option value="">-- Chọn Đại đội --</option>
                                            </select>
                                        </div>
                                        <div class="col-md">
                                            <label class="small text-muted">5. Trung đội</label>
                                            <select class="form-select form-control unit-selector" data-level="trung-doi" id="unit_trung_doi" disabled>
                                                <option value="">-- Chọn Trung đội --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="unit_id" id="final_unit_id" value="{{ old('unit_id') }}" required>
                                    @error('unit_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_date">Ngày huấn luyện</label>
                                    <input type="date" class="form-control" id="training_date" name="training_date" value="{{ old('training_date', $today->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="day_of_week">Thứ</label>
                                    <input type="text" class="form-control" id="day_of_week" name="day_of_week" value="{{ old('day_of_week', $dayOfWeek) }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nội dung huấn luyện -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Nội dung & Quân số</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="training_content">Nội dung huấn luyện</label>
                                    <textarea class="form-control" id="training_content" name="training_content" rows="3">{{ old('training_content') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="required_quanso">Quân số phải có</label>
                                    <input type="number" class="form-control" id="required_quanso" name="required_quanso" value="{{ old('required_quanso', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="actual_quanso">Quân số hiện có</label>
                                    <input type="number" class="form-control" id="actual_quanso" name="actual_quanso" value="{{ old('actual_quanso', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="required_hours">Thời gian phải huấn luyện (giờ)</label>
                                    <input type="number" class="form-control" id="required_hours" name="required_hours" value="{{ old('required_hours', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="actual_hours">Thời gian đã huấn luyện (giờ)</label>
                                    <input type="number" class="form-control" id="actual_hours" name="actual_hours" value="{{ old('actual_hours', 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kết quả kiểm tra -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Kết quả kiểm tra</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="test_quanso">Quân số kiểm tra</label>
                                    <input type="number" class="form-control" id="test_quanso" name="test_quanso" value="{{ old('test_quanso', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="good_count">Số lượng Giỏi</label>
                                    <input type="number" class="form-control" id="good_count" name="good_count" value="{{ old('good_count', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="fair_count">Số lượng Khá</label>
                                    <input type="number" class="form-control" id="fair_count" name="fair_count" value="{{ old('fair_count', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="pass_count">Số lượng Đạt</label>
                                    <input type="number" class="form-control" id="pass_count" name="pass_count" value="{{ old('pass_count', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="fail_count">Số lượng Không đạt</label>
                                    <input type="number" class="form-control" id="fail_count" name="fail_count" value="{{ old('fail_count', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="rating">Xếp loại chung</label>
                                    <select class="form-select" id="rating" name="rating">
                                        <option value="">-- Xếp loại --</option>
                                        <option value="xuất_sắc" {{ old('rating') == 'xuất_sắc' ? 'selected' : '' }}>Xuất sắc</option>
                                        <option value="giỏi" {{ old('rating') == 'giỏi' ? 'selected' : '' }}>Giỏi</option>
                                        <option value="khá" {{ old('rating') == 'khá' ? 'selected' : '' }}>Khá</option>
                                        <option value="trung_bình" {{ old('rating') == 'trung_bình' ? 'selected' : '' }}>Trung bình</option>
                                        <option value="yếu" {{ old('rating') == 'yếu' ? 'selected' : '' }}>Yếu</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đánh giá và Ghi chú -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Đánh giá & Tài liệu</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="general_evaluation">Nhận xét, đánh giá chung</label>
                                    <textarea class="form-control" id="general_evaluation" name="general_evaluation" rows="3">{{ old('general_evaluation') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="instructor">Người trực tiếp huấn luyện</label>
                                    <input type="text" class="form-control" id="instructor" name="instructor" value="{{ old('instructor') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="commander">Người chỉ huy/Kiểm tra</label>
                                    <input type="text" class="form-control" id="commander" name="commander" value="{{ old('commander') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="attachment">Tài liệu đính kèm</label>
                                    <input type="file" class="form-control" id="attachment" name="attachment">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">Ghi chú thêm</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-5">
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-success btn-lg">Lưu nhật ký huấn luyện</button>
                        <a href="{{ route('training-logs.index') }}" class="btn btn-danger btn-lg">Hủy bỏ</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
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

    $('#training_date').on('change', function() {
        const date = new Carbon($(this).val());
        // Simple day of week mapper
        const days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        const dayName = days[new Date($(this).val()).getDay()];
        $('#day_of_week').val(dayName);
    });
});
</script>
@endsection
