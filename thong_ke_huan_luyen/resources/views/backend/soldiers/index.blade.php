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
    <div class="page-header animate__animated animate__fadeInDown">
        <h3 class="fw-bold mb-3">Quản lý Quân nhân</h3>
        @include('backend.include.breadcrumbs', ['activeLabel' => 'Quân nhân', 'activeRoute' => route('soldiers.index')])
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card animate__animated animate__fadeInUp">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Danh sách quân nhân</h4>
                        <div class="ms-auto d-flex align-items-center">
                            <div class="dropdown me-2" id="bulk-actions-wrapper" style="display: none;">
                                <button class="btn btn-secondary dropdown-toggle btn-round" type="button" id="bulkActionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-tasks"></i> Thao tác hàng loạt (<span id="selected-count">0</span>)
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="bulkActionDropdown">
                                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="bulkDelete()"><i class="fas fa-trash-alt me-2"></i> Xóa đã chọn</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#bulkMoveModal"><i class="fas fa-exchange-alt me-2"></i> Chuyển đơn vị</a></li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-info btn-round me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import"></i> Nhập từ Excel
                            </button>
                            <a href="{{ route('soldiers.export-excel', request()->all()) }}" class="btn btn-success btn-round me-2">
                                <i class="fas fa-file-excel"></i> Xuất Excel
                            </a>
                            <a href="{{ route('soldiers.export-pdf', request()->all()) }}" class="btn btn-danger btn-round me-2">
                                <i class="fas fa-file-pdf"></i> Xuất PDF
                            </a>
                            <a href="{{ route('soldiers.create') }}" class="btn btn-primary btn-round">
                                <i class="fa fa-plus"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Lọc theo đơn vị (Server-side trigger) -->
                    @if($units->count() > 1)
                    <form action="{{ route('soldiers.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group p-0">
                                    <label>Lọc theo đơn vị:</label>
                                    <select name="unit_id" class="form-select form-control" onchange="this.form.submit()">
                                        <option value="">-- Tất cả đơn vị --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->getFullHierarchyName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endif

                    <form id="bulk-action-form" action="{{ route('soldiers.bulk-action') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="action" id="bulk-action-type">
                        <input type="hidden" name="target_unit_id" id="bulk-target-unit-id">
                        <div id="bulk-selected-ids"></div>
                    </form>

                    <div class="table-responsive">
                        <table id="soldiers-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px">
                                        <div class="form-check p-0">
                                            <input type="checkbox" class="form-check-input" id="checkAll">
                                        </div>
                                    </th>
                                    <th>STT</th>
                                    <th>Họ và tên</th>
                                    <th>Cấp bậc</th>
                                    <th>Chức vụ</th>
                                    {{-- <th>Đơn vị</th> --}}
                                    <th>Ngày tháng năm sinh</th>
                                    <th>Tháng nhập ngũ</th>
                                    <th>Số hiệu quân nhân</th>
                                    <th>Ngày vào Đảng đoàn</th>
                                    <th>Học vấn Ngoại ngữ</th>
                                    <th>Nghề nghiệp bậc chuyên môn</th>
                                    <th>Hộ khẩu thường trú</th>
                                    <th>Khi cần báo tin cho ai? Ở đâu?</th>
                                    <th>Ghi chú (thay đổi)</th>
                                    <th style="width: 10%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($soldiers as $index => $soldier)
                                    <tr>
                                        <td>
                                            <div class="form-check p-0">
                                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $soldier->id }}">
                                            </div>
                                        </td>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $soldier->full_name }}</td>
                                        <td>{{ $soldier->rank }}</td>
                                        <td>{{ $soldier->position }}</td>
                                        {{-- <td>{{ $soldier->unit ? $soldier->unit->getFullHierarchyName() : 'N/A' }}</td> --}}
                                        <td>{{ $soldier->birth_date ? $soldier->birth_date->format('d/m/Y') : '' }}</td>
                                        <td>{{ $soldier->enlistment_date ? $soldier->enlistment_date->format('m/Y') : '' }}</td>
                                        <td>{{ $soldier->code }}</td>
                                        <td>{{ $soldier->party_join_date ? $soldier->party_join_date->format('d/m/Y') : '' }}</td>
                                        <td>{{ $soldier->education }} / {{ $soldier->foreign_language }}</td>
                                        <td>{{ $soldier->professional_level }}</td>
                                        <td>{{ $soldier->permanent_residence }}</td>
                                        <td>{{ $soldier->emergency_contact_name }} - {{ $soldier->emergency_contact_address }}</td>
                                        <td>{{ $soldier->notes }}</td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('soldiers.show', $soldier->id) }}" class="btn btn-link btn-info btn-lg" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                    <i class="fa fa-eye" style="color: white"></i>
                                                </a>
                                                <a href="{{ route('soldiers.edit', $soldier->id) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Sửa">
                                                    <i class="fa fa-edit" style="color: white"></i>
                                                </a>
                                                <button type="button" class="btn btn-link btn-danger delete-soldier-btn"
                                                    data-id="{{ $soldier->id }}"
                                                    data-name="{{ $soldier->full_name }}"
                                                    data-bs-toggle="tooltip" title="Xóa">
                                                    <i class="fa fa-times" style="color: white"></i>
                                                </button>
                                                <form id="delete-form-{{ $soldier->id }}" action="{{ route('soldiers.destroy', $soldier->id) }}" method="POST" style="display: none">
                                                    @csrf
                                                    @method('DELETE')
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

    <!-- Modal Chuyển đơn vị hàng loạt -->
    <div class="modal fade" id="bulkMoveModal" tabindex="-1" aria-labelledby="bulkMoveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkMoveModalLabel">Chuyển đơn vị cho các quân nhân đã chọn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn đơn vị mới:</label>
                        <select id="bulk-target-unit-select" class="form-select">
                            <option value="">-- Chọn đơn vị --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->getFullHierarchyName() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="executeBulkMove()">Xác nhận chuyển</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nhập từ Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Nhập danh sách quân nhân từ Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('soldiers.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Chọn file Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" name="file" class="form-control" required accept=".xlsx, .xls, .csv">
                        </div>
                        <div class="form-group mb-3">
                            <label>Đơn vị mặc định (nếu trong file không có tên đơn vị)</label>
                            <select name="unit_id" class="form-select">
                                <option value="">-- Chọn đơn vị --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->getFullHierarchyName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <p class="mb-1"><i class="fas fa-info-circle me-1"></i> <strong>Lưu ý:</strong></p>
                            <ul class="small mb-0">
                                <li>Sử dụng đúng các cột theo file mẫu.</li>
                                <li>Ngày tháng nhập theo định dạng: dd/mm/yyyy.</li>
                                <li>Dung lượng file tối đa 10MB.</li>
                            </ul>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('soldiers.download-template') }}" class="btn btn-link">
                                <i class="fas fa-download me-1"></i> Tải file Excel mẫu tại đây
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Bắt đầu nhập</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#soldiers-datatables').DataTable({
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                },
                "columnDefs": [
                    { "orderable": false, "targets": [0, 14] } // Vô hiệu hóa sắp xếp cho checkbox và thao tác
                ],
                "order": [[1, 'asc']] // Sắp xếp theo STT mặc định
            });

            // Xử lý chọn tất cả
            $('#checkAll').on('change', function() {
                $('.row-checkbox').prop('checked', $(this).is(':checked'));
                updateBulkActionMenu();
            });

            // Xử lý chọn từng dòng
            $(document).on('change', '.row-checkbox', function() {
                updateBulkActionMenu();

                // Cập nhật trạng thái checkbox "Chọn tất cả"
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
                var count = $('.row-checkbox:checked').length;
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: 'Bạn có chắc chắn muốn xóa ' + count + ' quân nhân đã chọn? Thao tác này không thể hoàn tác!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Đồng ý xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBulkAction('delete');
                    }
                });
            }

            window.executeBulkMove = function() {
                var targetUnitId = $('#bulk-target-unit-select').val();
                if (!targetUnitId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thiếu thông tin',
                        text: 'Vui lòng chọn đơn vị mới.'
                    });
                    return;
                }
                $('#bulk-target-unit-id').val(targetUnitId);
                submitBulkAction('change_unit');
            }

            // Xử lý xóa đơn lẻ
            $(document).on('click', '.delete-soldier-btn', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: 'Bạn có chắc chắn muốn xóa quân nhân: ' + name + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Đồng ý xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });

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
                const walk = (x - startX) * 2; // Tốc độ kéo
                slider.scrollLeft = scrollLeft - walk;
            });
        });
    </script>
@endsection
