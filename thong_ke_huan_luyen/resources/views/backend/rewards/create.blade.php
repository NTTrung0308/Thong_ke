@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Thêm khen thưởng mới</h3>
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
                <a href="{{ route('rewards.index') }}">Khen thưởng</a>
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
                    <div class="card-title">Thông tin khen thưởng</div>
                </div>
                <form action="{{ route('rewards.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Loại khen thưởng <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="unit" {{ old('type') == 'unit' ? 'selected' : '' }}>Khen thưởng đơn vị</option>
                                        <option value="superior" {{ old('type') == 'superior' ? 'selected' : '' }}>Khen thưởng cấp trên</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="d-block mb-2">Đơn vị khen thưởng <span class="text-danger">*</span></label>
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

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="soldier_id">Quân nhân (Nếu có)</label>
                                    <select class="form-select select2 @error('soldier_id') is-invalid @enderror" id="soldier_id" name="soldier_id">
                                        <option value="">-- Chọn quân nhân --</option>
                                        @foreach($soldiers as $soldier)
                                            <option value="{{ $soldier->id }}" {{ old('soldier_id') == $soldier->id ? 'selected' : '' }}>
                                                {{ $soldier->full_name }} ({{ $soldier->unit->name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('soldier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reward_form">Hình thức khen thưởng <span class="text-danger">*</span></label>
                                    <select class="form-select @error('reward_form') is-invalid @enderror" id="reward_form" name="reward_form" required>
                                        <option value="">-- Chọn hình thức --</option>
                                        @foreach($rewardForms as $form)
                                            <option value="{{ $form }}" {{ old('reward_form') == $form ? 'selected' : '' }}>{{ $form }}</option>
                                        @endforeach
                                    </select>
                                    @error('reward_form')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="reason">Lý do khen thưởng <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="decision_date">Ngày quyết định <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('decision_date') is-invalid @enderror" id="decision_date" name="decision_date" value="{{ old('decision_date') }}" required>
                                    @error('decision_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="decision_level">Cấp quyết định <span class="text-danger">*</span></label>
                                    <select class="form-select @error('decision_level') is-invalid @enderror" id="decision_level" name="decision_level" required>
                                        <option value="">-- Chọn cấp --</option>
                                        @foreach($decisionLevels as $level)
                                            <option value="{{ $level }}" {{ old('decision_level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                    @error('decision_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="decision_number">Số quyết định</label>
                                    <input type="text" class="form-control @error('decision_number') is-invalid @enderror" id="decision_number" name="decision_number" value="{{ old('decision_number') }}">
                                    @error('decision_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="signer_name">Người ký</label>
                                    <input type="text" class="form-control @error('signer_name') is-invalid @enderror" id="signer_name" name="signer_name" value="{{ old('signer_name') }}">
                                    @error('signer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="signer_position">Chức vụ người ký</label>
                                    <input type="text" class="form-control @error('signer_position') is-invalid @enderror" id="signer_position" name="signer_position" value="{{ old('signer_position') }}">
                                    @error('signer_position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="attachment">File đính kèm (PDF, Ảnh)</label>
                                    <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment">
                                    @error('attachment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="result">Kết quả/Ghi chú</label>
                                    <textarea class="form-control @error('result') is-invalid @enderror" id="result" name="result" rows="2">{{ old('result') }}</textarea>
                                    @error('result')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Lưu lại</button>
                        <a href="{{ route('rewards.index') }}" class="btn btn-danger">Hủy bỏ</a>
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

    // Khởi tạo select2 cho quân nhân
    if ($('.select2').length > 0) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }
});
</script>
@endsection
