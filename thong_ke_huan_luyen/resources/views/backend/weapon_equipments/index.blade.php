@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <style>
        .table-responsive {
            cursor: grab;
        }
        .table-responsive.dragging {
            cursor: grabbing;
            user-select: none;
        }
    </style>
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
                    <div class="card-title">DANH SÁCH VŨ KHÍ, TRANG BỊ BIÊN CHẾ</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="weapon-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Họ và tên</th>
                                    <th>AK</th>
                                    <th>RPD</th>
                                    <th>B41</th>
                                    <th>M79</th>
                                    <th>Thông nòng</th>
                                    <th>Phụ tùng</th>
                                    <th>Dây súng</th>
                                    <th>Hộp tiếp đạn</th>
                                    <th>Vịt dầu</th>
                                    <th>Bao đồ</th>
                                    <th>Áo súng</th>
                                    <th>Bịt nòng</th>
                                    <th>Kính ngắm</th>
                                    <th>Lựu đạn</th>
                                    <th>Xẻng BB</th>
                                    <th>Cuốc BB</th>
                                    <th>Ngày nhận/ký</th>
                                    <th>Ngày trả/ký</th>
                                    <th>Ghi chú (thay đổi)</th>
                                    <th style="width: 10%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($equipments as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->soldier->full_name }}</td>
                                        <td>{{ $item->ak }}</td>
                                        <td>{{ $item->rpd }}</td>
                                        <td>{{ $item->b41 }}</td>
                                        <td>{{ $item->m79 }}</td>
                                        <td>{{ $item->cleaning_rod }}</td>
                                        <td>{{ $item->spare_parts }}</td>
                                        <td>{{ $item->gun_strap }}</td>
                                        <td>{{ $item->magazine_box }}</td>
                                        <td>{{ $item->oil_can }}</td>
                                        <td>{{ $item->bag }}</td>
                                        <td>{{ $item->gun_cover }}</td>
                                        <td>{{ $item->muzzle_cover }}</td>
                                        <td>{{ $item->sight }}</td>
                                        <td>{{ $item->grenade }}</td>
                                        <td>{{ $item->infantry_shovel }}</td>
                                        <td>{{ $item->infantry_pickaxe }}</td>
                                        <td>
                                            {{ $item->receive_date ? $item->receive_date->format('d/m/Y') : '' }}
                                            @if($item->received_by) <br><small>({{ $item->received_by }})</small> @endif
                                        </td>
                                        <td>
                                            {{ $item->return_date ? $item->return_date->format('d/m/Y') : '' }}
                                            @if($item->returned_by) <br><small>({{ $item->returned_by }})</small> @endif
                                        </td>
                                        <td>{{ $item->notes }}</td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('weapon-equipments.edit', $item->id) }}" class="btn btn-link btn-primary" data-bs-toggle="tooltip" title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('weapon-equipments.destroy', $item->id) }}" method="POST" class="d-inline">
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
        $('#weapon-datatables').DataTable({
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": 21 }
            ]
        });

        // Kéo bảng sang ngang bằng chuột
        const slider = document.querySelector('.table-responsive');
        let isDown = false;
        let startX;
        let scrollLeft;

        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.classList.add('dragging');
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.classList.remove('dragging');
        });
        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.classList.remove('dragging');
        });
        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2;
            slider.scrollLeft = scrollLeft - walk;
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
