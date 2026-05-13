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

        #training-logs-datatables th {
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        #training-logs-datatables td {
            vertical-align: middle;
            text-align: center;
        }

        .text-left-important {
            text-align: left !important;
        }
    </style>
    <div class="page-header">
        <h3 class="fw-bold mb-3">Quản lý Nhật ký huấn luyện</h3>
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
                <a href="#">Huấn luyện</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('training-logs.index') }}">Nhật ký huấn luyện</a>
            </li>
        </ul>
        <div class="ms-md-auto py-2 py-md-0">
            {{-- <span class="text-muted me-3">Danh sách được tự động cập nhật theo quân nhân</span> --}}
            <a href="{{ route('training-logs.report') }}" class="btn btn-info btn-round me-2">
                <i class="fa fa-chart-bar"></i> Báo cáo
            </a>
            <a href="{{ route('training-logs.create') }}" class="btn btn-primary btn-round">
                <i class="fa fa-plus"></i> Thêm nhật ký bổ sung
            </a>
        </div>
    </div>

    <div class="row">
        {{-- <div class="col-sm-6 col-md-3">
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
        </div> --}}
        {{-- <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Nhật ký đã ghi</p>
                                <h4 class="card-title">{{ $stats['total_logs'] }}</h4>
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
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Quân số đạt</p>
                                <h4 class="card-title">{{ $stats['avg_result'] }}</h4>
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
                                <i class="fas fa-sync"></i>
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
        </div> --}}
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">NHẬT KÝ HUẤN LUYỆN</div>
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

                    <!-- Lọc theo đơn vị & Thời gian -->
                    <form action="{{ route('training-logs.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group p-0">
                                    <label>Đơn vị:</label>
                                    <select name="unit_id" class="form-select form-control" onchange="this.form.submit()">
                                        <option value="">-- Tất cả đơn vị --</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="training-logs-datatables" class="display table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th rowspan="3">STT</th>
                                    <th rowspan="3">Họ và tên</th>
                                    <th colspan="7">Chấm công, điểm danh, điểm quân số</th>
                                    <th rowspan="3">Thứ ngày tháng</th>
                                    <th rowspan="3">Nội dung huấn luyện</th>
                                    <th colspan="2">Quân số</th>
                                    <th colspan="2">Thời gian</th>
                                    <th colspan="9">Kết quả kiểm tra</th>
                                    <th rowspan="3">Xếp loại</th>
                                    <th rowspan="3" style="width: 10%">Thao tác</th>
                                </tr>
                                <tr>
                                    <th rowspan="2">Hai</th>
                                    <th rowspan="2">Ba</th>
                                    <th rowspan="2">Tư</th>
                                    <th rowspan="2">Năm</th>
                                    <th rowspan="2">Sáu</th>
                                    <th rowspan="2">Bảy</th>
                                    <th rowspan="2">CN</th>
                                    <th rowspan="2">Phải HL</th>
                                    <th rowspan="2">Đã HL</th>
                                    <th rowspan="2">Phải HL</th>
                                    <th rowspan="2">Đã HL</th>
                                    <th rowspan="2">QS KT</th>
                                    <th colspan="2">G</th>
                                    <th colspan="2">K</th>
                                    <th colspan="2">Đ</th>
                                    <th colspan="2">KĐ</th>
                                </tr>
                                <tr>
                                    <th>QS</th>
                                    <th>%</th>
                                    <th>QS</th>
                                    <th>%</th>
                                    <th>QS</th>
                                    <th>%</th>
                                    <th>QS</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trainingLogs as $index => $log)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($log->soldier)
                                                <strong>{{ $log->soldier->full_name }}</strong><br>
                                                {{-- <small class="text-muted">{{ $log->soldier->unit->name ?? 'N/A' }}</small> --}}
                                            @else
                                                <strong>{{ $log->unit_name_at_time }}</strong><br>
                                                <small class="text-muted">(Đơn vị)</small>
                                            @endif
                                        </td>
                                        <td>{{ $log->attendance_mon }}</td>
                                        <td>{{ $log->attendance_tue }}</td>
                                        <td>{{ $log->attendance_wed }}</td>
                                        <td>{{ $log->attendance_thu }}</td>
                                        <td>{{ $log->attendance_fri }}</td>
                                        <td>{{ $log->attendance_sat }}</td>
                                        <td>{{ $log->attendance_sun }}</td>
                                        <td>
                                            @if ($log->training_date)
                                                {{ $log->day_of_week }}, {{ $log->training_date->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-left-important">
                                            {{ $log->training_content ? Str::limit(strip_tags(html_entity_decode($log->training_content, ENT_QUOTES | ENT_HTML5, 'UTF-8')), 50) : '-' }}
                                        </td>
                                        <td>{{ $log->required_quanso }}</td>
                                        <td>{{ $log->actual_quanso }}</td>
                                        <td>{{ $log->required_hours }}</td>
                                        <td>{{ $log->actual_hours }}</td>
                                        <td>{{ $log->test_quanso }}</td>
                                        <td>{{ $log->good_count }}</td>
                                        <td>{{ $log->good_percent }}</td>
                                        <td>{{ $log->fair_count }}</td>
                                        <td>{{ $log->fair_percent }}</td>
                                        <td>{{ $log->pass_count }}</td>
                                        <td>{{ $log->pass_percent }}</td>
                                        <td>{{ $log->fail_count }}</td>
                                        <td>{{ $log->fail_percent }}</td>
                                        <td>
                                            @if ($log->training_content)
                                                <span class="badge {{ $log->rating_badge }}">
                                                    {{ $log->rating_name }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('training-logs.show', $log->id) }}"
                                                    class="btn btn-link btn-info btn-lg" data-bs-toggle="tooltip"
                                                    title="Xem chi tiết">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('training-logs.edit', $log->id) }}"
                                                    class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip"
                                                    title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('training-logs.destroy', $log->id) }}"
                                                    method="POST" id="delete-form-{{ $log->id }}"
                                                    style="display:inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button type="button" class="btn btn-link btn-danger"
                                                    data-bs-toggle="tooltip" title="Xóa"
                                                    onclick="confirmDelete({{ $log->id }})">
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
            $('#training-logs-datatables').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                },
                "columnDefs": [{
                    "orderable": false,
                    "targets": [2, 3, 4, 5, 6, 7, 8, 25]
                }],
                "order": [
                    [9, "desc"]
                ]
            });

            // Kéo bảng sang ngang bằng chuột
            const slider = document.querySelector('.table-responsive');
            if (slider) {
                let isDown = false;
                let startX;
                let scrollLeft;

                slider.addEventListener('mousedown', (e) => {
                    // Chỉ nhận chuột trái và không nhận trên các phần tử tương tác
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
        });

        function confirmDelete(logId) {
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
                    document.getElementById('delete-form-' + logId).submit();
                }
            });
        }
    </script>
@endsection
