@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Quản lý Đơn vị</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="fas fa-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Đơn vị</a>
            </li>
        </ul>
        <div class="ms-md-auto py-2 py-md-0">
            <a href="{{ route('units.create') }}" class="btn btn-primary btn-round">Thêm đơn vị mới</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Danh sách đơn vị</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="unit-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên đơn vị</th>
                                    <th>Cấp</th>
                                    <th>Đơn vị trực thuộc</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($units as $unit)
                                    <tr>
                                        <td>{{ $unit->id }}</td>
                                        <td>{{ $unit->name }}</td>
                                        <td>{{ $unit->level_label }}</td>
                                        <td>{{ $unit->parent ? $unit->parent->name : '-' }}</td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('units.edit', $unit->id) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-link btn-danger delete-btn" data-bs-toggle="tooltip" title="Xóa">
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
        // Khởi tạo DataTable
        $('#unit-datatables').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
            }
        });

        // Xử lý xóa với SweetAlert2
        $(document).on('click', '.delete-btn', function() {
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn không thể hoàn tác hành động này!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endsection
