@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Sửa kết quả tập huấn</h3>
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
                <a href="{{ route('training-results.index') }}">Tập huấn</a>
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
                    <h4 class="card-title">Sửa thông tin kết quả tập huấn</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('training-results.update', $trainingResult->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit_id">Đơn vị <span class="text-danger">*</span></label>
                                    <select class="form-control @error('unit_id') is-invalid @enderror" id="unit_id"
                                        name="unit_id" required>
                                        <option value="">-- Chọn đơn vị --</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ old('unit_id', $trainingResult->unit_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_date">Ngày tập huấn <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('training_date') is-invalid @enderror"
                                        id="training_date" name="training_date"
                                        value="{{ old('training_date', $trainingResult->training_date->format('Y-m-d')) }}"
                                        required>
                                    @error('training_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="content">Nội dung tập huấn <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="3"
                                        required placeholder="Nội dung bài học, chủ đề tập huấn...">{{ old('content', $trainingResult->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_time">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                        id="start_time" name="start_time"
                                        value="{{ old('start_time', $trainingResult->start_time->format('H:i')) }}"
                                        required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_time">Thời gian kết thúc <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                        id="end_time" name="end_time"
                                        value="{{ old('end_time', $trainingResult->end_time->format('H:i')) }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="trung_doi_count">Số Trung đội</label>
                                    <input type="number"
                                        class="form-control @error('trung_doi_count') is-invalid @enderror"
                                        id="trung_doi_count" name="trung_doi_count"
                                        value="{{ old('trung_doi_count', $trainingResult->trung_doi_count) }}"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="at_count">Số Tiểu đội (A)</label>
                                    <input type="number" class="form-control @error('at_count') is-invalid @enderror"
                                        id="at_count" name="at_count"
                                        value="{{ old('at_count', $trainingResult->at_count) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="kdt_count">Số Khẩu đội (KĐT)</label>
                                    <input type="number" class="form-control @error('kdt_count') is-invalid @enderror"
                                        id="kdt_count" name="kdt_count"
                                        value="{{ old('kdt_count', $trainingResult->kdt_count) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="result">Kết quả chung <span class="text-danger">*</span></label>
                                    <select class="form-control @error('result') is-invalid @enderror" id="result"
                                        name="result" required>
                                        <option value="xuất_sắc"
                                            {{ old('result', $trainingResult->result) == 'xuất_sắc' ? 'selected' : '' }}>
                                            Xuất sắc</option>
                                        <option value="giỏi"
                                            {{ old('result', $trainingResult->result) == 'giỏi' ? 'selected' : '' }}>Giỏi
                                        </option>
                                        <option value="khá"
                                            {{ old('result', $trainingResult->result) == 'khá' ? 'selected' : '' }}>Khá
                                        </option>
                                        <option value="trung_bình"
                                            {{ old('result', $trainingResult->result) == 'trung_bình' ? 'selected' : '' }}>
                                            Trung bình</option>
                                        <option value="yếu"
                                            {{ old('result', $trainingResult->result) == 'yếu' ? 'selected' : '' }}>Yếu
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="passing_rate">Tỷ lệ đạt (%)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('passing_rate') is-invalid @enderror" id="passing_rate"
                                        name="passing_rate"
                                        value="{{ old('passing_rate', $trainingResult->passing_rate) }}" min="0"
                                        max="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="instructor">Giáo viên/Người hướng dẫn</label>
                                    <input type="text" class="form-control @error('instructor') is-invalid @enderror"
                                        id="instructor" name="instructor"
                                        value="{{ old('instructor', $trainingResult->instructor) }}"
                                        placeholder="Họ tên, chức vụ">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="evaluation">Đánh giá chung</label>
                                    <textarea class="form-control" id="evaluation" name="evaluation" rows="2">{{ old('evaluation', $trainingResult->evaluation) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="result_details">Chi tiết kết quả</label>
                                    <textarea class="form-control" id="result_details" name="result_details" rows="2">{{ old('result_details', $trainingResult->result_details) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="strengths">Ưu điểm</label>
                                    <textarea class="form-control" id="strengths" name="strengths" rows="2">{{ old('strengths', $trainingResult->strengths) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="weaknesses">Khuyết điểm</label>
                                    <textarea class="form-control" id="weaknesses" name="weaknesses" rows="2">{{ old('weaknesses', $trainingResult->weaknesses) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="recommendations">Đề xuất, kiến nghị</label>
                                    <textarea class="form-control" id="recommendations" name="recommendations" rows="2">{{ old('recommendations', $trainingResult->recommendations) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supervisor">Người giám sát/Kiểm tra</label>
                                    <input type="text" class="form-control" id="supervisor" name="supervisor"
                                        value="{{ old('supervisor', $trainingResult->supervisor) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="attachment">Tài liệu đính kèm (PDF, Word)</label>
                                    <input type="file"
                                        class="form-control-file @error('attachment') is-invalid @enderror"
                                        id="attachment" name="attachment">
                                    @if ($trainingResult->attachment)
                                        <p class="mt-2 text-muted">File hiện tại: <a
                                                href="{{ Storage::url($trainingResult->attachment) }}"
                                                target="_blank">Xem tài liệu</a></p>
                                    @endif
                                    @error('attachment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card-action">
                            <button type="submit" class="btn btn-success">Cập nhật kết quả</button>
                            <a href="{{ route('training-results.index') }}" class="btn btn-danger">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="https://cdn.tiny.cloud/1/8s4hqaa7an28jigjhd5vzvhjwyiid21n0lczimuwgobsmr8m/tinymce/8/tinymce.min.js"
    referrerpolicy="origin"></script>
<script>
    $(document).ready(function() {
        // TinyMCE initialization
        tinymce.init({
            selector: '#content',
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace vertical-align visualblocks code fullscreen insertdatetime media table help wordcount',
            toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            language: 'vi',
            promotion: false,
            branding: false,
            height: 300
        });
    });
</script>
@endsection
