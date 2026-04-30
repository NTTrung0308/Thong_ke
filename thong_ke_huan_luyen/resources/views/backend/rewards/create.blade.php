@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Thêm khen thưởng</h3>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Loại khen thưởng <span class="text-danger">*</span></label>
                                    <select name="type" id="reward_type" class="form-control" required>
                                        <option value="unit">Khen thưởng đơn vị</option>
                                        <option value="superior">Khen thưởng quân nhân</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Đơn vị <span class="text-danger">*</span></label>
                                    <select name="unit_id" id="unit_id" class="form-control" required>
                                        <option value="">-- Chọn đơn vị --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="soldier_select_row" style="display: none;">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Quân nhân</label>
                                    <select name="soldier_id" id="soldier_id" class="form-control select2">
                                        <option value="">-- Chọn quân nhân --</option>
                                        @foreach($soldiers as $soldier)
                                            <option value="{{ $soldier->id }}" data-unit="{{ $soldier->unit_id }}">
                                                {{ $soldier->full_name }} ({{ $soldier->code }}) - {{ $soldier->unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hình thức khen thưởng <span class="text-danger">*</span></label>
                                    <select name="reward_form" class="form-control" required>
                                        <option value="">-- Chọn hình thức --</option>
                                        @foreach($rewardForms as $form)
                                            <option value="{{ $form }}">{{ $form }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cấp quyết định <span class="text-danger">*</span></label>
                                    <select name="decision_level" class="form-control" required>
                                        <option value="">-- Chọn cấp quyết định --</option>
                                        @foreach($decisionLevels as $level)
                                            <option value="{{ $level }}">{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Số quyết định</label>
                                    <input type="text" name="decision_number" class="form-control" placeholder="Ví dụ: 123/QĐ-BT">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ngày quyết định <span class="text-danger">*</span></label>
                                    <input type="date" name="decision_date" class="form-control" required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tệp đính kèm (PDF, Ảnh)</label>
                                    <input type="file" name="attachment" class="form-control-file">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Người ký</label>
                                    <input type="text" name="signer_name" class="form-control" placeholder="Họ tên người ký">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Chức vụ người ký</label>
                                    <input type="text" name="signer_position" class="form-control" placeholder="Ví dụ: Chính ủy">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Lý do khen thưởng <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required placeholder="Nhập lý do khen thưởng..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Ghi chú kết quả</label>
                            <textarea name="result" class="form-control" rows="2" placeholder="Ghi chú thêm kết quả nếu có..."></textarea>
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
        // Xử lý ẩn hiện chọn quân nhân
        $('#reward_type').change(function() {
            if ($(this).val() == 'superior') {
                $('#soldier_select_row').show();
                $('#soldier_id').attr('required', true);
            } else {
                $('#soldier_select_row').hide();
                $('#soldier_id').attr('required', false);
                $('#soldier_id').val('');
            }
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
            $('#soldier_id').val('');
        });
    });
</script>
@endsection
