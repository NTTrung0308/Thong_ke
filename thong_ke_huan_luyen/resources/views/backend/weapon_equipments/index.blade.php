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
                <a href="{{ route('weapon-equipments.menu') }}">Vũ khí trang bị</a>
            </li>
        </ul>
        <div class="ms-md-auto py-2 py-md-0">
            {{-- <span class="text-muted me-3">Danh sách được tự động cập nhật theo quân nhân</span> --}}
            <a href="{{ route('weapon-equipments.create') }}" class="btn btn-primary btn-round">Thêm trang bị bổ sung</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Tổng quân nhân</p>
                                <h4 class="card-title">{{ $stats['total_soldiers'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-crosshairs"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Súng (AK/RPD/B41...)</p>
                                <h4 class="card-title">
                                    {{ $stats['ak_count'] + $stats['rpd_count'] + $stats['b41_count'] + $stats['m79_count'] }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-bomb"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Tổng lựu đạn</p>
                                <h4 class="card-title">{{ $stats['grenade_total'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                <i class="fas fa-tools"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Trạng thái</p>
                                <h4 class="card-title">Đã đồng bộ</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">DANH SÁCH VŨ KHÍ, TRANG BỊ THEO QUÂN NHÂN</div>
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
                                    <th>Tình trạng</th>
                                    <th>Ngày nhận/Ký</th>
                                    <th>Ngày trả/Ký</th>
                                    <th>Ghi chú</th>
                                    <th style="width: 10%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($equipments as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->soldier->full_name }}</strong><br>
                                            {{-- <small class="text-muted">ĐVBC: {{ $item->soldier->unit->name ?? 'N/A' }}</small><br>
                                            <small class="text-success">ĐVQL: {{ $item->unit->getFullHierarchyName() ?? 'N/A' }}</small> --}}
                                        </td>
                                        <td>{{ $item->ak ?? '-' }}</td>
                                        <td>{{ $item->rpd ?? '-' }}</td>
                                        <td>{{ $item->b41 ?? '-' }}</td>
                                        <td>{{ $item->m79 ?? '-' }}</td>
                                        <td>{{ $item->cleaning_rod ?? '-' }}</td>
                                        <td>{{ $item->spare_parts ?? '-' }}</td>
                                        <td>{{ $item->gun_strap ?? '-' }}</td>
                                        <td>{{ $item->magazine_box ?? '-' }}</td>
                                        <td>{{ $item->oil_can ?? '-' }}</td>
                                        <td>{{ $item->bag ?? '-' }}</td>
                                        <td>{{ $item->gun_cover ?? '-' }}</td>
                                        <td>{{ $item->muzzle_cover ?? '-' }}</td>
                                        <td>{{ $item->sight ?? '-' }}</td>
                                        <td>{{ $item->grenade ?? '-' }}</td>
                                        <td>{{ $item->infantry_shovel ?? '-' }}</td>
                                        <td>{{ $item->infantry_pickaxe ?? '-' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = 'badge-success';
                                                $conditionName = 'Tốt';
                                                if($item->condition == 'hỏng') {
                                                    $badgeClass = 'badge-danger';
                                                    $conditionName = 'Hỏng';
                                                } elseif($item->condition == 'cần_bảo_dưỡng') {
                                                    $badgeClass = 'badge-warning';
                                                    $conditionName = 'Cần bảo dưỡng';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $conditionName }}</span>
                                        </td>
                                        <td> {{ $item->receive_date ? $item->receive_date->format('d/m/Y') : '' }}
                                            @if ($item->received_by)
                                                <br><small>({{ $item->received_by }})</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->return_date ? $item->return_date->format('d/m/Y') : '' }}
                                            @if ($item->returned_by)
                                                <br><small>({{ $item->returned_by }})</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->notes }}</td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('weapon-equipments.edit', $item->id) }}"
                                                    class="btn btn-link btn-primary" data-bs-toggle="tooltip"
                                                    title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('weapon-equipments.destroy', $item->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-link btn-danger delete-btn"
                                                        data-bs-toggle="tooltip" title="Xóa">
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
                "columnDefs": [{
                    "orderable": false,
                    "targets": 21
                }]
            });

            // Kéo bảng sang ngang bằng chuột
            const slider = document.querySelector('.table-responsive');
            if (slider) {
                let isDown = false;
                let startX;
                let scrollLeft;

                slider.addEventListener('mousedown', (e) => {
                    // Chỉ nhận chuột trái và không nhận trên các phần tử tương tác (nút, link)
                    if (e.button !== 0 || e.target.closest(
                            'a, button, .dataTables_length, .dataTables_filter')) return;

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
                    const walk = (x - startX) * 2; // Tốc độ kéo
                    slider.scrollLeft = scrollLeft - walk;
                });
            }

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
