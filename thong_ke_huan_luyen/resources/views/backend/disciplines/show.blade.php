@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chi tiết kỷ luật</h3>
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
                <a href="#">Chi tiết</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Quyết định kỷ luật: {{ $discipline->decision_number }}</h4>
                        <div class="ms-auto">
                            <a href="{{ route('disciplines.edit', $discipline->id) }}" class="btn btn-primary btn-round me-2">
                                <i class="fa fa-edit"></i> Sửa
                            </a>
                            <a href="{{ route('disciplines.index') }}" class="btn btn-black btn-round">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3 text-primary">Thông tin quân nhân & Đơn vị</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 35%">Quân nhân</th>
                                    <td>
                                        <strong>{{ $discipline->soldier_name_at_time }}</strong><br>
                                        <small>Số hiệu: {{ $discipline->soldier->code ?? 'N/A' }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Cấp bậc khi đó</th>
                                    <td>{{ $discipline->soldier_rank_at_time }}</td>
                                </tr>
                                <tr>
                                    <th>Đơn vị quản lý</th>
                                    <td>{{ $discipline->unit_name_at_time }}</td>
                                </tr>
                                <tr>
                                    <th>Trạng thái hiện tại</th>
                                    <td>
                                        <span class="badge {{ $discipline->status_badge }}">
                                            {{ $discipline->status_name }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3 text-primary">Thông tin quyết định</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 35%">Số quyết định</th>
                                    <td>{{ $discipline->decision_number }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày quyết định</th>
                                    <td>{{ $discipline->formatted_decision_date }}</td>
                                </tr>
                                <tr>
                                    <th>Hình thức kỷ luật</th>
                                    <td class="text-danger fw-bold">{{ $discipline->discipline_form }}</td>
                                </tr>
                                <tr>
                                    <th>Cấp quyết định</th>
                                    <td>{{ $discipline->decision_level }}</td>
                                </tr>
                                <tr>
                                    <th>Người ký</th>
                                    <td>{{ $discipline->signer_name }} ({{ $discipline->signer_position }})</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3 text-primary">Thời gian thi hành</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 35%">Ngày bắt đầu</th>
                                    <td>{{ $discipline->formatted_execution_date }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày hết hiệu lực</th>
                                    <td>{{ $discipline->formatted_expiry_date }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($discipline->attachment)
                            <h5 class="fw-bold mb-3 text-primary">Tài liệu đính kèm</h5>
                            <a href="{{ asset('storage/' . $discipline->attachment) }}" target="_blank" class="btn btn-outline-info">
                                <i class="fa fa-file-pdf"></i> Xem văn bản quyết định
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="fw-bold text-primary">Nội dung sai phạm:</h5>
                        <div class="p-3 bg-light rounded border-start border-danger border-4">
                            <p class="fw-bold mb-1">{{ $discipline->work_content }}</p>
                            <hr>
                            {!! nl2br(e($discipline->violation_details)) !!}
                        </div>
                    </div>

                    @if($discipline->improvement_measures)
                    <div class="mt-4">
                        <h5 class="fw-bold text-primary">Biện pháp khắc phục:</h5>
                        <div class="p-3 bg-light rounded border-start border-success border-4">
                            {!! nl2br(e($discipline->improvement_measures)) !!}
                        </div>
                    </div>
                    @endif

                    @if($discipline->result)
                    <div class="mt-4">
                        <h5 class="fw-bold text-primary">Ghi chú kết quả:</h5>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($discipline->result)) !!}
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer text-muted">
                    <small>Được tạo bởi: {{ $discipline->creator->name ?? 'N/A' }} lúc {{ $discipline->created_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>
@endsection
