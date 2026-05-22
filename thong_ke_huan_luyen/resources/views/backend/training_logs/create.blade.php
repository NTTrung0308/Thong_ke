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
                            <div class="col-md-12 mb-3">
                                <div class="card shadow-none border">
                                    <div class="card-body py-2">
                                        <div class="row align-items-end g-2">
                                            <div class="col-md-3">
                                                <div class="form-group p-0 mb-0">
                                                    <label class="small mb-1">1. Môn học</label>
                                                    <div class="input-group">
                                                        <select class="form-select form-control-sm" id="subject_id">
                                                            <option value="">-- Chọn môn học --</option>
                                                        </select>
                                                        <button class="btn btn-outline-primary btn-sm btn-quick-add" type="button" data-type="subject" title="Thêm môn học mới"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group p-0 mb-0">
                                                    <label class="small mb-1">2. Bài</label>
                                                    <div class="input-group">
                                                        <select class="form-select form-control-sm" id="lesson_id" disabled>
                                                            <option value="">-- Chọn bài --</option>
                                                        </select>
                                                        <button class="btn btn-outline-primary btn-sm btn-quick-add" type="button" data-type="lesson" title="Thêm bài mới" disabled><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group p-0 mb-0">
                                                    <label class="small mb-1">3. Nội dung bài</label>
                                                    <div class="input-group">
                                                        <select class="form-select form-control-sm" id="content_id" disabled>
                                                            <option value="">-- Chọn nội dung --</option>
                                                        </select>
                                                        <button class="btn btn-outline-primary btn-sm btn-quick-add" type="button" data-type="content" title="Thêm nội dung mới" disabled><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 d-flex gap-1">
                                                <button type="button" class="btn btn-primary btn-sm flex-fill" id="add_to_content">
                                                    <i class="fas fa-check"></i> Xác nhận chọn
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

    <!-- Modal Thêm Nội dung Huấn luyện -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Môn học / Bài / Nội dung</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSubjectForm">
                        @csrf
                        <div class="form-group">
                            <label>Cấp đơn vị</label>
                            <select class="form-select" id="modal_unit_level" name="unit_level">
                                @if(Auth::user()->hasRole('chi-huy') || Auth::user()->hasRole('trung-doan') || Auth::user()->hasRole('tieu-doan') || Auth::user()->hasRole('dai-doi'))
                                    <option value="dai-doi">Đại đội</option>
                                @endif
                                <option value="trung-doi">Trung đội</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Thuộc (Môn/Bài)</label>
                            <select class="form-select" id="modal_parent_id" name="parent_id">
                                <option value="">-- Là Môn học mới --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tên (Môn/Bài/Nội dung)</label>
                            <input type="text" class="form-control" id="modal_subject_name" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveSubjectBtn">Lưu lại</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/8s4hqaa7an28jigjhd5vzvhjwyiid21n0lczimuwgobsmr8m/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
$(document).ready(function() {
    // TinyMCE initialization
    tinymce.init({
        selector: '#training_content, #general_evaluation',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace vertical-align visualblocks code fullscreen insertdatetime media table help wordcount',
        toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        language: 'vi',
        promotion: false,
        branding: false,
        height: 300
    });

    const levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];

    // Load initial subjects
    function loadSubjects(level = 'dai-doi') {
        $.get(`{{ route('training-subjects.index') }}?unit_level=${level}`, function(data) {
            let options = '<option value="">-- Chọn môn học --</option>';
            data.forEach(s => {
                options += `<option value="${s.id}">${s.name}</option>`;
            });
            $('#subject_id').html(options);
            $('#lesson_id').html('<option value="">-- Chọn bài --</option>').prop('disabled', true);
            $('#content_id').html('<option value="">-- Chọn nội dung --</option>').prop('disabled', true);
            $('.btn-quick-add[data-type="lesson"], .btn-quick-add[data-type="content"]').prop('disabled', true);
        });
    }

    // Load subjects based on selected unit level
    function updateSubjectsBySelectedUnit() {
        const unitDaiDoi = $('#unit_dai_doi').val();
        const unitTrungDoi = $('#unit_trung_doi').val();

        let selectedLevel = '';
        if (unitTrungDoi) {
            selectedLevel = 'trung-doi';
        } else if (unitDaiDoi) {
            selectedLevel = 'dai-doi';
        }

        if (selectedLevel) {
            loadSubjects(selectedLevel);
            $('.btn-quick-add[data-type="subject"]').prop('disabled', false);
        } else {
            $('#subject_id').html('<option value="">-- Chọn môn học --</option>');
            $('#lesson_id').html('<option value="">-- Chọn bài --</option>').prop('disabled', true);
            $('#content_id').html('<option value="">-- Chọn nội dung --</option>').prop('disabled', true);
            $('.btn-quick-add').prop('disabled', true);
        }
    }
    // Initial load
    updateSubjectsBySelectedUnit();

    $('#subject_id').on('change', function() {
        const parentId = $(this).val();
        if (parentId) {
            const unitDaiDoi = $('#unit_dai_doi').val();
            const unitTrungDoi = $('#unit_trung_doi').val();
            let selectedLevel = unitTrungDoi ? 'trung-doi' : 'dai-doi';
            $.get(`{{ route('training-subjects.children', '') }}/${parentId}?unit_level=${selectedLevel}`, function(data) {
                let options = '<option value="">-- Chọn bài --</option>';
                data.forEach(s => {
                    options += `<option value="${s.id}">${s.name}</option>`;
                });
                $('#lesson_id').html(options).prop('disabled', false);
                $('.btn-quick-add[data-type="lesson"]').prop('disabled', false);
                $('#content_id').html('<option value="">-- Chọn nội dung --</option>').prop('disabled', true);
                $('.btn-quick-add[data-type="content"]').prop('disabled', true);
            });
        } else {
            $('#lesson_id').html('<option value="">-- Chọn bài --</option>').prop('disabled', true);
            $('.btn-quick-add[data-type="lesson"]').prop('disabled', true);
            $('#content_id').html('<option value="">-- Chọn nội dung --</option>').prop('disabled', true);
            $('.btn-quick-add[data-type="content"]').prop('disabled', true);
        }
    });

    $('#lesson_id').on('change', function() {
        const parentId = $(this).val();
        if (parentId) {
            const unitDaiDoi = $('#unit_dai_doi').val();
            const unitTrungDoi = $('#unit_trung_doi').val();
            let selectedLevel = unitTrungDoi ? 'trung-doi' : 'dai-doi';
            $.get(`{{ route('training-subjects.children', '') }}/${parentId}?unit_level=${selectedLevel}`, function(data) {
                let options = '<option value="">-- Chọn nội dung --</option>';
                data.forEach(s => {
                    options += `<option value="${s.id}">${s.name}</option>`;
                });
                $('#content_id').html(options).prop('disabled', false);
                $('.btn-quick-add[data-type="content"]').prop('disabled', false);
            });
        } else {
            $('#content_id').html('<option value="">-- Chọn nội dung --</option>').prop('disabled', true);
            $('.btn-quick-add[data-type="content"]').prop('disabled', true);
        }
    });

    $('#add_to_content').on('click', function() {
        const subject = $('#subject_id option:selected').text();
        const lesson = $('#lesson_id option:selected').val() ? $('#lesson_id option:selected').text() : '';
        const content = $('#content_id option:selected').val() ? $('#content_id option:selected').text() : '';

        if (!$('#subject_id').val()) {
            Swal.fire('Cảnh báo', 'Vui lòng chọn ít nhất một môn học', 'warning');
            return;
        }

        let textToAdd = `<p><strong>- ${subject}</strong></p>`;
        if (lesson) textToAdd += `<p>Bài: ${lesson}</p>`;
        if (content) textToAdd += `<p> ${content}</p>`;

        const editor = tinymce.get('training_content');
        const currentContent = editor.getContent();
        editor.setContent(currentContent + textToAdd);
    });

    // Quick add logic
    $('.btn-quick-add').on('click', function() {
        const type = $(this).data('type');
        let title = 'Thêm môn học mới';
        let parentId = null;

        const unitDaiDoi = $('#unit_dai_doi').val();
        const unitTrungDoi = $('#unit_trung_doi').val();
        let unitLevel = unitTrungDoi ? 'trung-doi' : 'dai-doi';
        if (type === 'lesson') {
            title = 'Thêm bài học mới';
            parentId = $('#subject_id').val();
            if (!parentId) {
                Swal.fire('Cảnh báo', 'Vui lòng chọn môn học trước', 'warning');
                return;
            }
        } else if (type === 'content') {
            title = 'Thêm nội dung bài mới';
            parentId = $('#lesson_id').val();
            if (!parentId) {
                Swal.fire('Cảnh báo', 'Vui lòng chọn bài học trước', 'warning');
                return;
            }
        }

        Swal.fire({
            title: title,
            input: 'text',
            inputPlaceholder: 'Nhập tên...',
            showCancelButton: true,
            confirmButtonText: 'Lưu lại',
            cancelButtonText: 'Hủy',
            inputValidator: (value) => {
                if (!value) return 'Tên không được để trống!';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`{{ route('training-subjects.store') }}`, {
                    _token: '{{ csrf_token() }}',
                    name: result.value,
                    parent_id: parentId,
                    unit_level: unitLevel
                }, function(response) {
                    if (response.success) {
                        Swal.fire('Thành công', response.message, 'success');
                        if (type === 'subject') {
                            updateSubjectsBySelectedUnit();
                        } else if (type === 'lesson') {
                            $('#subject_id').trigger('change');
                        } else if (type === 'content') {
                            $('#lesson_id').trigger('change');
                        }
                    }
                });
            }
        });
    });

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
        updateSubjectsBySelectedUnit();

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
