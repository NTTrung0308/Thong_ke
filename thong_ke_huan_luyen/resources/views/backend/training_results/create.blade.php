@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Thêm kết quả tập huấn</h3>
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
                <a href="{{ route('training-results.index') }}">Kết quả tập huấn</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Thêm mới</a>
            </li>
        </ul>
    </div>

    <form action="{{ route('training-results.store') }}" method="POST" enctype="multipart/form-data">
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
                                    <label class="d-block mb-2">Đơn vị tập huấn <span class="text-danger">*</span></label>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="training_date">Ngày tập huấn <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('training_date') is-invalid @enderror" id="training_date" name="training_date" value="{{ old('training_date', date('Y-m-d')) }}" required>
                                    @error('training_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_time">Giờ bắt đầu <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                    @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_time">Giờ kết thúc <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                    @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nội dung và Quân số -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Nội dung & Quân số tham gia</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="content">Nội dung tập huấn <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('content') is-invalid @enderror" id="content" name="content" value="{{ old('content') }}" required placeholder="Ví dụ: Tập huấn kỹ thuật chiến đấu bộ binh...">
                                    @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="trung_doi_count">Số lượng Trung đội <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('trung_doi_count') is-invalid @enderror" id="trung_doi_count" name="trung_doi_count" value="{{ old('trung_doi_count', 0) }}" required min="0">
                                    @error('trung_doi_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="at_count">Số lượng Tiểu đội (A) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('at_count') is-invalid @enderror" id="at_count" name="at_count" value="{{ old('at_count', 0) }}" required min="0">
                                    @error('at_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kdt_count">Số lượng Khẩu đội <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('kdt_count') is-invalid @enderror" id="kdt_count" name="kdt_count" value="{{ old('kdt_count', 0) }}" required min="0">
                                    @error('kdt_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kết quả và Đánh giá -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Kết quả đánh giá</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="result">Kết quả chung <span class="text-danger">*</span></label>
                                    <select class="form-select @error('result') is-invalid @enderror" id="result" name="result" required>
                                        <option value="">-- Chọn kết quả --</option>
                                        <option value="xuất_sắc" {{ old('result') == 'xuất_sắc' ? 'selected' : '' }}>Xuất sắc</option>
                                        <option value="giỏi" {{ old('result') == 'giỏi' ? 'selected' : '' }}>Giỏi</option>
                                        <option value="khá" {{ old('result') == 'khá' ? 'selected' : '' }}>Khá</option>
                                        <option value="trung_bình" {{ old('result') == 'trung_bình' ? 'selected' : '' }}>Trung bình</option>
                                        <option value="yếu" {{ old('result') == 'yếu' ? 'selected' : '' }}>Yếu</option>
                                    </select>
                                    @error('result') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="passing_rate">Tỷ lệ đạt yêu cầu (%)</label>
                                    <input type="number" step="0.01" class="form-control @error('passing_rate') is-invalid @enderror" id="passing_rate" name="passing_rate" value="{{ old('passing_rate') }}" min="0" max="100">
                                    @error('passing_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="attachment">Tài liệu đính kèm (PDF, Doc)</label>
                                    <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment">
                                    @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="instructor">Người phụ trách/Giáo viên</label>
                                    <input type="text" class="form-control" id="instructor" name="instructor" value="{{ old('instructor') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supervisor">Người kiểm tra/Giám sát</label>
                                    <input type="text" class="form-control" id="supervisor" name="supervisor" value="{{ old('supervisor') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="result_details">Chi tiết kết quả</label>
                                    <textarea class="form-control" id="result_details" name="result_details" rows="3">{{ old('result_details') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="evaluation">Đánh giá chung</label>
                                    <textarea class="form-control" id="evaluation" name="evaluation" rows="3">{{ old('evaluation') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="strengths">Ưu điểm</label>
                                    <textarea class="form-control" id="strengths" name="strengths" rows="3">{{ old('strengths') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="weaknesses">Khuyết điểm/Tồn tại</label>
                                    <textarea class="form-control" id="weaknesses" name="weaknesses" rows="3">{{ old('weaknesses') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="recommendations">Biện pháp khắc phục/Đề xuất</label>
                                    <textarea class="form-control" id="recommendations" name="recommendations" rows="3">{{ old('recommendations') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-5">
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-success btn-lg">Lưu kết quả tập huấn</button>
                        <a href="{{ route('training-results.index') }}" class="btn btn-danger btn-lg">Hủy bỏ</a>
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
});
</script>
@endsection
