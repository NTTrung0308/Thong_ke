@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chỉnh sửa kỷ luật</h3>
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
                <a href="{{ route('disciplines.index') }}">Kỷ luật</a>
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
                    <div class="card-title">Cập nhật thông tin kỷ luật</div>
                </div>
                <form action="{{ route('disciplines.update', $discipline->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Đơn vị <span class="text-danger">*</span></label>
                                    <select name="unit_id" id="unit_id" class="form-control" required>
                                        <option value="">-- Chọn đơn vị --</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ $discipline->unit_id == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Quân nhân <span class="text-danger">*</span></label>
                                    <select name="soldier_id" id="soldier_id" class="form-control select2" required>
                                        @foreach ($soldiers as $soldier)
                                            <option value="{{ $soldier->id }}" data-unit="{{ $soldier->unit_id }}"
                                                {{ $discipline->soldier_id == $soldier->id ? 'selected' : '' }}>
                                                {{ $soldier->full_name }} ({{ $soldier->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hình thức kỷ luật <span class="text-danger">*</span></label>
                                    <select name="discipline_form" class="form-control" required>
                                        <option value="">-- Chọn hình thức --</option>
                                        @foreach ($disciplineForms as $form)
                                            <option value="{{ $form }}"
                                                {{ $discipline->discipline_form == $form ? 'selected' : '' }}>
                                                {{ $form }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cấp quyết định <span class="text-danger">*</span></label>
                                    <select name="decision_level" class="form-control" required>
                                        <option value="">-- Chọn cấp quyết định --</option>
                                        @foreach ($decisionLevels as $level)
                                            <option value="{{ $level }}"
                                                {{ $discipline->decision_level == $level ? 'selected' : '' }}>
                                                {{ $level }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Số quyết định</label>
                                    <input type="text" name="decision_number" class="form-control"
                                        value="{{ $discipline->decision_number }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ngày quyết định <span class="text-danger">*</span></label>
                                    <input type="date" name="decision_date" class="form-control" required
                                        value="{{ $discipline->decision_date->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Trạng thái <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ $discipline->status == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ngày bắt đầu thi hành</label>
                                    <input type="date" name="execution_date" class="form-control"
                                        value="{{ $discipline->execution_date ? $discipline->execution_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ngày hết hiệu lực (dự kiến)</label>
                                    <input type="date" name="expiry_date" class="form-control"
                                        value="{{ $discipline->expiry_date ? $discipline->expiry_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Người ký</label>
                                    <input type="text" name="signer_name" class="form-control"
                                        value="{{ $discipline->signer_name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Chức vụ người ký</label>
                                    <input type="text" name="signer_position" class="form-control"
                                        value="{{ $discipline->signer_position }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Nội dung công việc/Sai phạm <span class="text-danger">*</span></label>
                            <input type="text" name="work_content" class="form-control" required
                                value="{{ $discipline->work_content }}">
                        </div>

                        <div class="form-group">
                            <label>Chi tiết sai phạm <span class="text-danger">*</span></label>
                            <textarea name="violation_details" id="violation_details" class="form-control" rows="4" required>{{ $discipline->violation_details }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Biện pháp khắc phục</label>
                            <textarea name="improvement_measures" class="form-control" rows="2">{{ $discipline->improvement_measures }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ghi chú kết quả</label>
                                    <textarea name="result" class="form-control" rows="2">{{ $discipline->result }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tệp đính kèm (Để trống nếu giữ nguyên)</label>
                                    <input type="file" name="attachment" class="form-control-file">
                                    @if ($discipline->attachment)
                                        <small class="text-muted">Đã có tệp:
                                            {{ basename($discipline->attachment) }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                        <a href="{{ route('disciplines.index') }}" class="btn btn-danger">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.tiny.cloud/1/8s4hqaa7an28jigjhd5vzvhjwyiid21n0lczimuwgobsmr8m/tinymce/8/tinymce.min.js" referrerpolicy="origin"
        crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // TinyMCE initialization
            tinymce.init({
                selector: '#violation_details',
                plugins: 'advlist autolink lists link image charmap preview anchor searchreplace vertical-align visualblocks code fullscreen insertdatetime media table help wordcount',
                toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                language: 'vi',
                promotion: false,
                branding: false,
                height: 300
            });
            // Lọc quân nhân theo đơn vị
            $('#unit_id').change(function() {
                var unitId = $(this).val();
                if (unitId) {
                    $('#soldier_id option').each(function() {
                        if ($(this).data('unit') == unitId || $(this).val() == "") {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                } else {
                    $('#soldier_id option').show();
                }

                var currentSoldierUnit = $('#soldier_id option:selected').data('unit');
                if (currentSoldierUnit != unitId) {
                    $('#soldier_id').val('');
                }
            });
        });
    </script>
@endsection
