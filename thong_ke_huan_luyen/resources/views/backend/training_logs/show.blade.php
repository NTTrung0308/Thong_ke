@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chi tiết Nhật ký huấn luyện</h3>
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
                <a href="#">Chi tiết</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Thông tin nhật ký ngày {{ $trainingLog->formatted_training_date }}</h4>
                        <div class="ms-auto">
                            <a href="{{ route('training-logs.edit', $trainingLog->id) }}"
                                class="btn btn-primary btn-round me-2">
                                <i class="fa fa-edit"></i> Sửa
                            </a>
                            <a href="{{ route('training-logs.index') }}" class="btn btn-secondary btn-round">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Đơn vị</th>
                                    <td>{{ $trainingLog->unit ? $trainingLog->unit->getFullHierarchyName() : ($trainingLog->unit_name_at_time ?? 'N/A') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày huấn luyện</th>
                                    <td>{{ $trainingLog->formatted_training_date }} ({{ $trainingLog->day_of_week }})</td>
                                </tr>
                                <tr>
                                    <th>Người phụ trách</th>
                                    <td>{{ $trainingLog->instructor ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Chỉ huy ký</th>
                                    <td>{{ $trainingLog->commander ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Xếp loại</th>
                                    <td><span
                                            class="badge {{ $trainingLog->rating_badge }}">{{ $trainingLog->rating_name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Quân số tổng</th>
                                    <td>{{ $trainingLog->required_quanso }}</td>
                                </tr>
                                <tr>
                                    <th>Quân số tham gia</th>
                                    <td>{{ $trainingLog->actual_quanso }} ({{ $trainingLog->attendance_rate }}%)</td>
                                </tr>
                                <tr>
                                    <th>Thời gian (Thực tế/Quy định)</th>
                                    <td>{{ $trainingLog->actual_hours }}/{{ $trainingLog->required_hours }} giờ
                                        ({{ $trainingLog->time_rate }}%)</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-info card-annoucement card-round">
                                <div class="card-body text-center">
                                    <div class="annoucement-title">
                                        Nội dung huấn luyện
                                    </div>
                                    <div class="annoucement-desc">
                                        {!! html_entity_decode($trainingLog->training_content, ENT_QUOTES | ENT_HTML5, 'UTF-8') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4 class="fw-bold">Kết quả kiểm tra chi tiết</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>QS kiểm tra</th>
                                            <th>Giỏi</th>
                                            <th>Khá</th>
                                            <th>Trung bình</th>
                                            <th>Yếu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $trainingLog->test_quanso }}</td>
                                            <td>{{ $trainingLog->good_count }} ({{ $trainingLog->good_percent }}%)</td>
                                            <td>{{ $trainingLog->fair_count }} ({{ $trainingLog->fair_percent }}%)</td>
                                            <td>{{ $trainingLog->pass_count }} ({{ $trainingLog->pass_percent }}%)</td>
                                            <td>{{ $trainingLog->fail_count }} ({{ $trainingLog->fail_percent }}%)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4 class="fw-bold">Điểm danh trong tuần</h4>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($trainingLog->attendance_array as $day => $status)
                                    <div class="border p-2 rounded text-center" style="min-width: 80px;">
                                        <div class="fw-bold">{{ $day }}</div>
                                        <div class="mt-1 h4">
                                            @if ($status == '+')
                                                <span class="text-success" title="Có mặt"><i
                                                        class="fa fa-check-circle"></i></span>
                                            @elseif($status == 'x')
                                                <span class="text-warning" title="Vắng có lý do"><i
                                                        class="fa fa-user-clock"></i></span>
                                            @elseif($status == '-')
                                                <span class="text-danger" title="Vắng không lý do"><i
                                                        class="fa fa-user-times"></i></span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if ($trainingLog->general_evaluation)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h4 class="fw-bold">Nhận xét chung</h4>
                                <div class="p-3 bg-light rounded border">
                                    {!! $trainingLog->general_evaluation !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($trainingLog->notes)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h4 class="fw-bold">Ghi chú</h4>
                                <div class="p-3 bg-light rounded border">
                                    {{ $trainingLog->notes }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($trainingLog->attachment)
                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <a href="{{ asset($trainingLog->attachment) }}" class="btn btn-info btn-round"
                                    target="_blank">
                                    <i class="fa fa-file-download"></i> Xem tài liệu đính kèm
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer text-muted small text-end">
                    Được tạo bởi: {{ $trainingLog->creator ? $trainingLog->creator->name : 'N/A' }} lúc
                    {{ $trainingLog->created_at->format('H:i d/m/Y') }}
                    @if ($trainingLog->updated_by)
                        | Cập nhật bởi: {{ $trainingLog->updater ? $trainingLog->updater->name : 'N/A' }} lúc
                        {{ $trainingLog->updated_at->format('H:i d/m/Y') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
