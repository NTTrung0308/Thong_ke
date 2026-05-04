@extends('backend.layouts.dashboard')

@section('dashboard_content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Chỉnh sửa Nhật ký huấn luyện</h3>
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
            <a href="#">Chỉnh sửa</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <form action="{{ route('training-logs.update', $trainingLog->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin chung</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit_id">Đơn vị <span class="text-danger">*</span></label>
                                <select class="form-control" id="unit_id" name="unit_id" required>
                                    <option value="">-- Chọn đơn vị --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id', $trainingLog->unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="training_date">Ngày huấn luyện <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="training_date" name="training_date" value="{{ old('training_date', $trainingLog->training_date->format('Y-m-d')) }}" required>
                                @error('training_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="day_of_week">Thứ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="day_of_week" name="day_of_week" value="{{ old('day_of_week', $trainingLog->day_of_week) }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Điểm danh trong tuần (nếu có)</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach(['mon' => 'Thứ 2', 'tue' => 'Thứ 3', 'wed' => 'Thứ 4', 'thu' => 'Thứ 5', 'fri' => 'Thứ 6', 'sat' => 'Thứ 7', 'sun' => 'Chủ nhật'] as $key => $label)
                                <div class="form-group border p-2 rounded">
                                    <label>{{ $label }}</label>
                                    <select name="attendance_{{ $key }}" class="form-control form-control-sm">
                                        <option value="">-</option>
                                        <option value="+" {{ old('attendance_'.$key, $trainingLog->{'attendance_'.$key}) == '+' ? 'selected' : '' }}>+</option>
                                        <option value="x" {{ old('attendance_'.$key, $trainingLog->{'attendance_'.$key}) == 'x' ? 'selected' : '' }}>x</option>
                                        <option value="-" {{ old('attendance_'.$key, $trainingLog->{'attendance_'.$key}) == '-' ? 'selected' : '' }}>-</option>
                                    </select>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">Nội dung & Quân số</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="training_content">Nội dung huấn luyện <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="training_content" name="training_content" rows="3" required>{{ old('training_content', $trainingLog->training_content) }}</textarea>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="required_quanso">Quân số tổng <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="required_quanso" name="required_quanso" value="{{ old('required_quanso', $trainingLog->required_quanso) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="actual_quanso">Quân số tham gia <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="actual_quanso" name="actual_quanso" value="{{ old('actual_quanso', $trainingLog->actual_quanso) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="required_hours">Thời gian quy định (giờ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="required_hours" name="required_hours" value="{{ old('required_hours', $trainingLog->required_hours) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="actual_hours">Thời gian thực tế (giờ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="actual_hours" name="actual_hours" value="{{ old('actual_hours', $trainingLog->actual_hours) }}" min="0" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">Kết quả kiểm tra & Đánh giá</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="test_quanso">QS kiểm tra</label>
                                <input type="number" class="form-control" id="test_quanso" name="test_quanso" value="{{ old('test_quanso', $trainingLog->test_quanso) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="good_count">Giỏi</label>
                                <input type="number" class="form-control" id="good_count" name="good_count" value="{{ old('good_count', $trainingLog->good_count) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fair_count">Khá</label>
                                <input type="number" class="form-control" id="fair_count" name="fair_count" value="{{ old('fair_count', $trainingLog->fair_count) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="pass_count">Trung bình</label>
                                <input type="number" class="form-control" id="pass_count" name="pass_count" value="{{ old('pass_count', $trainingLog->pass_count) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fail_count">Yếu</label>
                                <input type="number" class="form-control" id="fail_count" name="fail_count" value="{{ old('fail_count', $trainingLog->fail_count) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="rating">Xếp loại chung <span class="text-danger">*</span></label>
                                <select class="form-control" id="rating" name="rating" required>
                                    <option value="xuất_sắc" {{ old('rating', $trainingLog->rating) == 'xuất_sắc' ? 'selected' : '' }}>Xuất sắc</option>
                                    <option value="giỏi" {{ old('rating', $trainingLog->rating) == 'giỏi' ? 'selected' : '' }}>Giỏi</option>
                                    <option value="khá" {{ old('rating', $trainingLog->rating) == 'khá' ? 'selected' : '' }}>Khá</option>
                                    <option value="trung_bình" {{ old('rating', $trainingLog->rating) == 'trung_bình' ? 'selected' : '' }}>Trung bình</option>
                                    <option value="yếu" {{ old('rating', $trainingLog->rating) == 'yếu' ? 'selected' : '' }}>Yếu</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="general_evaluation">Nhận xét chung</label>
                        <textarea class="form-control" id="general_evaluation" name="general_evaluation" rows="2">{{ old('general_evaluation', $trainingLog->general_evaluation) }}</textarea>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="instructor">Người phụ trách huấn luyện</label>
                                <input type="text" class="form-control" id="instructor" name="instructor" value="{{ old('instructor', $trainingLog->instructor) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="commander">Chỉ huy ký tên</label>
                                <input type="text" class="form-control" id="commander" name="commander" value="{{ old('commander', $trainingLog->commander) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="attachment">Tài liệu đính kèm (PDF, DOC)</label>
                                <input type="file" class="form-control" id="attachment" name="attachment">
                                @if($trainingLog->attachment)
                                    <small class="text-muted">Đã có file: <a href="{{ Storage::url($trainingLog->attachment) }}" target="_blank">Xem tài liệu</a></small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('training-logs.index') }}" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Cập nhật nhật ký</button>
                </div>
            </div>
        </form>
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

        $('#training_date').on('change', function() {
            const date = new Date($(this).val());
            const days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
            $('#day_of_week').val(days[date.getDay()]);
        });
    });
</script>
@endsection
