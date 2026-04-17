@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Quản lý Quân nhân</h3>
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
                <a href="#">Danh sách</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Danh sách quân nhân</h4>
                        <a href="{{ route('soldiers.create') }}" class="btn btn-primary btn-round ms-auto">
                            <i class="fa fa-plus"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Lọc theo đơn vị (Server-side trigger) -->
                    <form action="{{ route('soldiers.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group p-0">
                                    <label>Lọc theo đơn vị:</label>
                                    <select name="unit_id" class="form-select form-control" onchange="this.form.submit()">
                                        <option value="">-- Tất cả đơn vị --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="soldiers-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Số hiệu</th>
                                    <th>Họ và tên</th>
                                    <th>Cấp bậc</th>
                                    <th>Chức vụ</th>
                                    <th>Đơn vị</th>
                                    <th style="width: 10%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($soldiers as $index => $soldier)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $soldier->code }}</td>
                                        <td>{{ $soldier->full_name }}</td>
                                        <td>{{ $soldier->rank }}</td>
                                        <td>{{ $soldier->position }}</td>
                                        <td>{{ $soldier->unit->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('soldiers.show', $soldier->id) }}" class="btn btn-link btn-info btn-lg" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('soldiers.edit', $soldier->id) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('soldiers.destroy', $soldier->id) }}" method="POST" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa quân nhân này?')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#soldiers-datatables').DataTable({
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                },
                "columnDefs": [
                    { "orderable": false, "targets": 6 } // Vô hiệu hóa sắp xếp cho cột Thao tác
                ]
            });
        });
    </script>
@endsection
