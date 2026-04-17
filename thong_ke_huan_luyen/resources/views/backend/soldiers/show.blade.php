@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chi tiết quân nhân</h3>
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
                <a href="{{ route('soldiers.index') }}">Quân nhân</a>
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
                        <h4 class="card-title">Thông tin: {{ $soldier->full_name }}</h4>
                        <div class="ms-auto">
                            <a href="{{ route('soldiers.edit', $soldier->id) }}" class="btn btn-primary btn-round me-2">
                                <i class="fa fa-edit"></i> Sửa
                            </a>
                            <a href="{{ route('soldiers.index') }}" class="btn btn-info btn-round">
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
                                    <th width="40%">Số hiệu quân nhân</th>
                                    <td>{{ $soldier->code }}</td>
                                </tr>
                                <tr>
                                    <th>Họ và tên</th>
                                    <td>{{ $soldier->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>Cấp bậc</th>
                                    <td>{{ $soldier->rank }}</td>
                                </tr>
                                <tr>
                                    <th>Chức vụ</th>
                                    <td>{{ $soldier->position }}</td>
                                </tr>
                                <tr>
                                    <th>Đơn vị</th>
                                    <td>{{ $soldier->unit->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày sinh</th>
                                    <td>{{ \Carbon\Carbon::parse($soldier->birth_date)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày nhập ngũ</th>
                                    <td>{{ \Carbon\Carbon::parse($soldier->enlistment_date)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày vào Đảng/Đoàn</th>
                                    <td>{{ $soldier->party_join_date ? \Carbon\Carbon::parse($soldier->party_join_date)->format('d/m/Y') : 'Chưa vào' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Trình độ văn hóa</th>
                                    <td>{{ $soldier->education }}</td>
                                </tr>
                                <tr>
                                    <th>Ngoại ngữ</th>
                                    <td>{{ $soldier->foreign_language ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Trình độ chuyên môn</th>
                                    <td>{{ $soldier->professional_level ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Người báo tin khi cần</th>
                                    <td>{{ $soldier->emergency_contact_name }}</td>
                                </tr>
                                <tr>
                                    <th>Địa chỉ người báo tin</th>
                                    <td>{{ $soldier->emergency_contact_address }}</td>
                                </tr>
                                <tr>
                                    <th>Hộ khẩu thường trú</th>
                                    <td>{{ $soldier->permanent_residence }}</td>
                                </tr>
                                <tr>
                                    <th>Ghi chú</th>
                                    <td>{{ $soldier->notes ?? 'Không có' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
