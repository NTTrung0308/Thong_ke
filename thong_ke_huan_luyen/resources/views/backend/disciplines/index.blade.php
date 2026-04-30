@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Quản lý kỷ luật</h3>
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
                <a href="#">Kỷ luật</a>
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
                        <h4 class="card-title">Danh sách kỷ luật</h4>
                        <div class="ms-auto">
                            <a href="{{ route('disciplines.report') }}" class="btn btn-info btn-round me-2">
                                <i class="fa fa-chart-bar"></i>
                                Báo cáo thống kê
                            </a>
                            <a href="{{ route('disciplines.create') }}" class="btn btn-primary btn-round">
                                <i class="fa fa-plus"></i>
                                Thêm kỷ luật
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Bộ lọc -->
                    <form method="GET" action="{{ route('disciplines.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Đơn vị</label>
                                    <select name="unit_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả đơn vị</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Hình thức</label>
                                    <select name="discipline_form" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả</option>
                                        @foreach($disciplineForms as $form)
                                            <option value="{{ $form }}" {{ request('discipline_form') == $form ? 'selected' : '' }}>
                                                {{ $form }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Năm</label>
                                    <select name="year" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả năm</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả</option>
                                        @foreach($statuses as $value => $label)
                                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="disciplines-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Ngày QĐ</th>
                                    <th>Số QĐ</th>
                                    <th>Quân nhân</th>
                                    <th>Đơn vị</th>
                                    <th>Hình thức</th>
                                    <th>Trạng thái</th>
                                    <th style="width: 10%">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($disciplines as $discipline)
                                    <tr>
                                        <td>{{ $discipline->formatted_decision_date }}</td>
                                        <td>{{ $discipline->decision_number }}</td>
                                        <td>
                                            <strong>{{ $discipline->soldier_name_at_time }}</strong><br>
                                            <small>{{ $discipline->soldier_rank_at_time }}</small>
                                        </td>
                                        <td>{{ $discipline->unit_name_at_time }}</td>
                                        <td>{{ $discipline->discipline_form }}</td>
                                        <td>
                                            <span class="badge {{ $discipline->status_badge }}">
                                                {{ $discipline->status_name }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('disciplines.show', $discipline->id) }}"
                                                    class="btn btn-link btn-info btn-lg" data-bs-toggle="tooltip"
                                                    title="Xem chi tiết">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('disciplines.edit', $discipline->id) }}"
                                                    class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip"
                                                    title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('disciplines.destroy', $discipline->id) }}" method="POST"
                                                    id="delete-form-{{ $discipline->id }}" style="display:inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button type="button" class="btn btn-link btn-danger"
                                                    data-bs-toggle="tooltip" title="Xóa"
                                                    onclick="confirmDelete({{ $discipline->id }})">
                                                    <i class="fa fa-times"></i>
                                                </button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#disciplines-datatables').DataTable({
                "pageLength": 10,
                "order": [[0, "desc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                }
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Bạn sẽ không thể hoàn tác hành động này!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Có, xóa nó!',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
