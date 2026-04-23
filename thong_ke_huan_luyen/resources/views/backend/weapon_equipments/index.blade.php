@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Quản lý Vũ khí - Trang bị</h3>
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
                <a href="#">Vũ khí trang bị</a>
            </li>
        </ul>
        <div class="ms-md-auto py-2 py-md-0">
            <a href="{{ route('weapon-equipments.create') }}" class="btn btn-primary btn-round">Thêm trang bị mới</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">LOẠI VŨ KHÍ, TRANG BỊ</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="weapon-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Quân nhân</th>
                                    <th>Đơn vị</th>
                                    <th>Vũ khí biên chế</th>
                                    <th>Ngày nhận</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($equipments as $item)
                                    <tr>
                                        <td>{{ $item->soldier->full_name }}</td>
                                        <td>{{ $item->unit->name }}</td>
                                        <td>
                                            @php
                                                $weapons = [];
                                                if ($item->ak) $weapons[] = "AK: $item->ak";
                                                if ($item->rpd) $weapons[] = "RPD: $item->rpd";
                                                if ($item->b41) $weapons[] = "B41: $item->b41";
                                                if ($item->m79) $weapons[] = "M79: $item->m79";
                                            @endphp
                                            {{ implode(', ', $weapons) ?: 'Chưa có' }}
                                        </td>
                                        <td>{{ $item->receive_date ? $item->receive_date->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            @if($item->status == 'dang-su-dung')
                                                <span class="badge badge-success">Đang sử dụng</span>
                                            @else
                                                <span class="badge badge-warning">{{ $item->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('weapon-equipments.edit', $item->id) }}" class="btn btn-link btn-primary" title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('weapon-equipments.destroy', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-link btn-danger delete-btn" title="Xóa">
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
        $('#weapon-datatables').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
            }
        });

        $(document).on('click', '.delete-btn', function() {
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Xác nhận xóa biên chế này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý',
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
