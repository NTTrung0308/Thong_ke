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
        <h3 class="fw-bold mb-3">Quản lý kỷ luật</h3>
        @include('backend.include.breadcrumbs', ['activeLabel' => 'Kỷ luật', 'activeRoute' => route('disciplines.index')])
        <div class="ms-md-auto py-2 py-md-0">
            {{-- <span class="text-muted me-3">Danh sách được tự động cập nhật theo quân nhân</span> --}}
            <a href="{{ route('disciplines.export-excel', request()->all()) }}" class="btn btn-success btn-round me-2">
                <i class="fa fa-file-excel"></i> Xuất Excel
            </a>
            <a href="{{ route('disciplines.export-pdf', request()->all()) }}" class="btn btn-danger btn-round me-2">
                <i class="fa fa-file-pdf"></i> Xuất PDF
            </a>
            <a href="{{ route('disciplines.report') }}" class="btn btn-info btn-round me-2">
                <i class="fa fa-chart-bar"></i> Báo cáo thống kê
            </a>
            <a href="{{ route('disciplines.create') }}" class="btn btn-primary btn-round">
                <i class="fa fa-plus"></i> Thêm kỷ luật bổ sung
            </a>
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
                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                <i class="fas fa-gavel"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Tổng kỷ luật</p>
                                <h4 class="card-title">{{ $stats['total_disciplines'] }}</h4>
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
                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Đang thi hành</p>
                                <h4 class="card-title">{{ $stats['pending_disciplines'] }}</h4>
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
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Hoàn thành</p>
                                <h4 class="card-title">{{ $stats['completed_disciplines'] }}</h4>
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
                    <div class="card-title">DANH SÁCH THEO DÕI KỶ LUẬT</div>
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
                            {{-- <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tìm kiếm chung</label>
                                    <input type="text" name="search" class="form-control" placeholder="Quân nhân, số quyết định, nội dung..." value="{{ request('search') }}">
                                </div>
                            </div> --}}
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hình thức</label>
                                    <select name="discipline_form" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả hình thức</option>
                                        @foreach($disciplineForms as $form)
                                            <option value="{{ $form }}" {{ request('discipline_form') == $form ? 'selected' : '' }}>
                                                {{ $form }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả trạng thái</option>
                                        @foreach($statuses as $value => $label)
                                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Từ ngày</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" onchange="this.form.submit()">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Đến ngày</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" onchange="this.form.submit()">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('disciplines.index') }}" class="btn btn-secondary ms-2">
                                        <i class="fa fa-redo"></i> Xóa lọc
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="disciplines-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Họ và tên</th>
                                    <th>Nội dung vi phạm</th>
                                    <th>Hình thức & Quyết định</th>
                                    <th>Trạng thái</th>
                                    <th style="width: 10%">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($disciplines as $index => $discipline)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($discipline->soldier)
                                                <strong>{{ $discipline->soldier->full_name }}</strong><br>
                                                {{-- <small class="text-muted">ĐVBC: {{ $discipline->soldier->unit->name ?? 'N/A' }}</small><br>
                                                <small class="text-danger">ĐVKL: {{ $discipline->unit->getFullHierarchyName() ?? 'N/A' }}</small> --}}
                                            @else
                                                <strong>{{ $discipline->unit->name ?? $discipline->unit_name_at_time }}</strong><br>
                                                <small class="text-muted">(Khen thưởng tập thể)</small><br>
                                                <small class="text-danger">ĐVKL: {{ $discipline->unit ? $discipline->unit->getFullHierarchyName() : 'N/A' }}</small>
                                            @endif
                                        </td>
                                        <td>{!!$discipline->violation_details ? Str::limit($discipline->violation_details, 50) : '-' !!}</td>
                                        <td>
                                            @if($discipline->discipline_form)
                                                <strong>{{ $discipline->discipline_form }}</strong><br>
                                                <small>
                                                    Ngày: {{ $discipline->decision_date ? $discipline->decision_date->format('d/m/Y') : '...' }}<br>
                                                    Số: {{ $discipline->decision_number ?? '...' }}
                                                </small>
                                            @else
                                                <span class="text-muted">Chưa cập nhật</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($discipline->discipline_form)
                                                <span class="badge {{ $discipline->status_badge }}">
                                                    {{ $discipline->status_name }}
                                                </span>
                                            @else
                                                -
                                            @endif
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
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                },
                "columnDefs": [
                    { "orderable": false, "targets": 5 }
                ]
            });

            // Kéo bảng sang ngang bằng chuột
            const slider = document.querySelector('.table-responsive');
            if (slider) {
                let isDown = false;
                let startX;
                let scrollLeft;

                slider.addEventListener('mousedown', (e) => {
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
                    const walk = (x - startX) * 2;
                    slider.scrollLeft = scrollLeft - walk;
                });
            }
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

