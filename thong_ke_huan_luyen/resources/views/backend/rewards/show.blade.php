@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chi tiết khen thưởng</h3>
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
                <a href="#">Chi tiết</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Quyết định khen thưởng: {{ $reward->decision_number }}</h4>
                        <div class="ms-auto">
                            <a href="{{ route('rewards.edit', $reward->id) }}" class="btn btn-primary btn-round me-2">
                                <i class="fa fa-edit"></i> Sửa
                            </a>
                            <a href="{{ route('rewards.index') }}" class="btn btn-black btn-round">
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
                                    <th style="width: 30%">Loại</th>
                                    <td>{{ $reward->type_name }}</td>
                                </tr>
                                <tr>
                                    <th>Đơn vị</th>
                                    <td>
                                        {{ $reward->unit ? $reward->unit->getFullHierarchyName() : 'N/A' }}
                                    </td>
                                </tr>
                                @if ($reward->type == 'superior')
                                    <tr>
                                        <th>Quân nhân</th>
                                        <td>{{ $reward->soldier_name_at_time }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Hình thức</th>
                                    <td>{{ $reward->reward_form }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Số quyết định</th>
                                    <td>{{ $reward->decision_number }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày quyết định</th>
                                    <td>{{ $reward->formatted_decision_date }}</td>
                                </tr>
                                <tr>
                                    <th>Cấp quyết định</th>
                                    <td>{{ $reward->decision_level }}</td>
                                </tr>
                                <tr>
                                    <th>Người ký</th>
                                    <td>{{ $reward->signer_name }} ({{ $reward->signer_position }})</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="fw-bold">Lý do khen thưởng:</h5>
                        <div class="p-3 bg-light rounded">
                            {{ $reward->reason }}
                        </div>
                    </div>

                    @if ($reward->result)
                        <div class="mt-4">
                            <h5 class="fw-bold">Kết quả:</h5>
                            <div class="p-3 bg-light rounded">
                                {{ $reward->result }}
                            </div>
                        </div>
                    @endif

                    @if ($reward->attachment)
                        <div class="mt-4">
                            <h5 class="fw-bold">Tệp đính kèm:</h5>
                            <a href="{{ asset($reward->attachment) }}" target="_blank" class="btn btn-info">
                                <i class="fa fa-file-download"></i> Xem/Tải xuống tệp đính kèm
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-footer text-muted">
                    <small>Được tạo bởi: {{ $reward->creator->name ?? 'N/A' }} lúc
                        {{ $reward->created_at->format('d/m/Y H:i') }}</small>
                    @if ($reward->updated_at > $reward->created_at)
                        <br><small>Cập nhật lần cuối lúc {{ $reward->updated_at->format('d/m/Y H:i') }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
