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
        <h3 class="fw-bold mb-3">Quản lý khen thưởng</h3>
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
                <a href="#">Khen thưởng</a>
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
                        <h4 class="card-title">Danh sách khen thưởng</h4>
                        <div class="ms-auto">
                            <a href="{{ route('rewards.report') }}" class="btn btn-info btn-round me-2">
                                <i class="fa fa-chart-bar"></i>
                                Báo cáo thống kê
                            </a>
                            <a href="{{ route('rewards.create') }}" class="btn btn-primary btn-round">
                                <i class="fa fa-plus"></i>
                                Thêm khen thưởng
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
                    <form method="GET" action="{{ route('rewards.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Loại</label>
                                    <select name="type" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả</option>
                                        <option value="unit" {{ request('type') == 'unit' ? 'selected' : '' }}>Khen thưởng đơn vị</option>
                                        <option value="superior" {{ request('type') == 'superior' ? 'selected' : '' }}>Khen thưởng cấp trên</option>
                                    </select>
                                </div>
                            </div>
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
                                    <label>Cấp quyết định</label>
                                    <select name="decision_level" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả các cấp</option>
                                        @foreach($decisionLevels as $level)
                                            <option value="{{ $level }}" {{ request('decision_level') == $level ? 'selected' : '' }}>
                                                {{ $level }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="rewards-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Nội dung khen thưởng (Lý do)</th>
                                    <th>Hình thức khen thưởng (Ngày, tháng, cấp quyết định)</th>
                                    <th style="width: 10%">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rewards as $index => $reward)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $reward->type == 'unit' ? $reward->unit_name_at_time : $reward->soldier_name_at_time }}</strong><br>
                                            {{ $reward->reason }}
                                        </td>
                                        <td>
                                            - Hình thức: {{ $reward->reward_form }}<br>
                                            - Ngày: {{ $reward->decision_date ? $reward->decision_date->format('d/m/Y') : ($reward->decision_month ?: '...') }}<br>
                                            - Cấp quyết định: {{ $reward->decision_level }} (Số: {{ $reward->decision_number }})
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('rewards.show', $reward->id) }}"
                                                    class="btn btn-link btn-info btn-lg" data-bs-toggle="tooltip"
                                                    title="Xem chi tiết">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('rewards.edit', $reward->id) }}"
                                                    class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip"
                                                    title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('rewards.destroy', $reward->id) }}" method="POST"
                                                    id="delete-form-{{ $reward->id }}" style="display:inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button type="button" class="btn btn-link btn-danger"
                                                    data-bs-toggle="tooltip" title="Xóa"
                                                    onclick="confirmDelete({{ $reward->id }})">
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
            $('#rewards-datatables').DataTable({
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                },
                "columnDefs": [
                    { "orderable": false, "targets": 3 }
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
