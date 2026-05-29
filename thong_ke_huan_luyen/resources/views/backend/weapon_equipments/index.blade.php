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
        @include('backend.include.breadcrumbs', ['activeLabel' => 'Vũ khí trang bị', 'activeRoute' => route('weapon-equipments.index')])
        <div class="ms-md-auto py-2 py-md-0">
            {{-- <span class="text-muted me-3">Danh sách được tự động cập nhật theo quân nhân</span> --}}
            <a href="{{ route('weapon-equipments.export-excel', request()->all()) }}" class="btn btn-success btn-round me-2">
                <i class="fa fa-file-excel"></i> Xuất Excel
            </a>
            <a href="{{ route('weapon-equipments.export-pdf', request()->all()) }}" class="btn btn-danger btn-round me-2">
                <i class="fa fa-file-pdf"></i> Xuất PDF
            </a>
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
                <div class="card-header d-flex align-items-center">
                    <div class="card-title">DANH SÁCH VŨ KHÍ, TRANG BỊ THEO QUÂN NHÂN</div>
                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown me-2" id="bulk-actions-wrapper" style="display: none;">
                            <button class="btn btn-secondary dropdown-toggle btn-round" type="button" id="bulkActionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-tasks"></i> Thao tác hàng loạt (<span id="selected-count">0</span>)
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="bulkActionDropdown">
                                <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#bulkConditionModal"><i class="fas fa-tools me-2"></i> Cập nhật tình trạng</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#bulkStatusModal"><i class="fas fa-info-circle me-2"></i> Cập nhật trạng thái</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="bulkDelete()"><i class="fas fa-trash-alt me-2"></i> Xóa đã chọn</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form id="bulk-action-form" action="{{ route('weapon-equipments.bulk-action') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="action" id="bulk-action-type">
                        <input type="hidden" name="condition" id="bulk-condition-input">
                        <input type="hidden" name="status" id="bulk-status-input">
                        <div id="bulk-selected-ids"></div>
                    </form>

                    <div class="table-responsive">
                        <table id="weapon-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px">
                                        <div class="form-check p-0">
                                            <input type="checkbox" class="form-check-input" id="checkAll">
                                        </div>
                                    </th>
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
                                        <td>
                                            <div class="form-check p-0">
                                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $item->id }}">
                                            </div>
                                        </td>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->soldier->full_name }}</strong>
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

    <!-- Modal Cập nhật tình trạng hàng loạt -->
    <div class="modal fade" id="bulkConditionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật tình trạng cho các mục đã chọn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn tình trạng:</label>
                        <select id="bulk-condition-select" class="form-select">
                            <option value="tốt">Tốt</option>
                            <option value="cần_bảo_dưỡng">Cần bảo dưỡng</option>
                            <option value="hỏng">Hỏng</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="executeBulkUpdate('condition')">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cập nhật trạng thái hàng loạt -->
    <div class="modal fade" id="bulkStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật trạng thái cho các mục đã chọn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn trạng thái:</label>
                        <select id="bulk-status-select" class="form-select">
                            <option value="dang-su-dung">Đang sử dụng</option>
                            <option value="dang-bao-quan">Đang bảo quản</option>
                            <option value="da-tra">Đã trả</option>
                            <option value="da-mat">Đã mất</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="executeBulkUpdate('status')">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#weapon-datatables').DataTable({
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                },
                "columnDefs": [{
                    "orderable": false,
                    "targets": [0, 23]
                }],
                "order": [[1, 'asc']]
            });

            // Xử lý chọn tất cả
            $('#checkAll').on('change', function() {
                $('.row-checkbox').prop('checked', $(this).is(':checked'));
                updateBulkActionMenu();
            });

            $(document).on('change', '.row-checkbox', function() {
                updateBulkActionMenu();
                var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
                $('#checkAll').prop('checked', allChecked);
            });

            function updateBulkActionMenu() {
                var selectedCount = $('.row-checkbox:checked').length;
                if (selectedCount > 0) {
                    $('#bulk-actions-wrapper').fadeIn();
                    $('#selected-count').text(selectedCount);
                } else {
                    $('#bulk-actions-wrapper').fadeOut();
                }
            }

            window.bulkDelete = function() {
                Swal.fire({
                    title: 'Xác nhận xóa ' + $('.row-checkbox:checked').length + ' bản ghi đã chọn?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBulkAction('delete');
                    }
                });
            }

            window.executeBulkUpdate = function(type) {
                if(type === 'condition') {
                    $('#bulk-condition-input').val($('#bulk-condition-select').val());
                    submitBulkAction('update_condition');
                } else {
                    $('#bulk-status-input').val($('#bulk-status-select').val());
                    submitBulkAction('update_status');
                }
            }

            function submitBulkAction(action) {
                var form = $('#bulk-action-form');
                $('#bulk-action-type').val(action);
                var container = $('#bulk-selected-ids');
                container.empty();
                $('.row-checkbox:checked').each(function() {
                    container.append('<input type="hidden" name="ids[]" value="' + $(this).val() + '">');
                });
                form.submit();
            }

            // Kéo bảng sang ngang bằng chuột
            const slider = document.querySelector('.table-responsive');
            if (slider) {
                let isDown = false;
                let startX;
                let scrollLeft;

                slider.addEventListener('mousedown', (e) => {
                    // Chỉ nhận chuột trái và không nhận trên các phần tử tương tác (nút, link)
                    if (e.button !== 0 || e.target.closest('a, button, .dataTables_length, .dataTables_filter')) return;

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
