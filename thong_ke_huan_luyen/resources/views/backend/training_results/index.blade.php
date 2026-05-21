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
        #training-results-table thead th {
            vertical-align: middle;
            text-align: center;
        }
    </style>
    <div class="page-header">
        <h3 class="fw-bold mb-3">Quản lý kết quả tập huấn</h3>
        @include('backend.include.breadcrumbs', ['activeLabel' => 'Kết quả tập huấn', 'activeRoute' => route('training-results.index')])
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Danh sách kết quả tập huấn</h4>
                        <div class="ms-auto">
                            <a href="{{ route('training-results.export-excel', request()->all()) }}" class="btn btn-success btn-round me-2">
                                <i class="fas fa-file-excel"></i> Xuất Excel
                            </a>
                            <a href="{{ route('training-results.export-pdf', request()->all()) }}" class="btn btn-danger btn-round me-2">
                                <i class="fas fa-file-pdf"></i> Xuất PDF
                            </a>
                            <a href="{{ route('training-results.create') }}" class="btn btn-primary btn-round">
                                <i class="fa fa-plus"></i> Thêm mới
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

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('training-results.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group p-0">
                                    <label>Đơn vị</label>
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

                    <div class="table-responsive">
                        <table id="training-results-table" class="display table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 5%">STT</th>
                                    <th rowspan="2">Đơn vị</th>
                                    <th rowspan="2">Ngày tháng</th>
                                    <th rowspan="2">Nội dung</th>
                                    <th rowspan="2">Thời gian (giờ)</th>
                                    <th colspan="2">Thành phần</th>
                                    <th rowspan="2">Kết quả</th>
                                    <th rowspan="2" style="width: 10%">Thao tác</th>
                                </tr>
                                <tr>
                                    <th>Trung đội</th>
                                    <th>AT, KĐT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trainingResults as $index => $result)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $result->unit->name }}</strong><br>
                                            <small class="text-muted">{{ $result->unit->getFullHierarchyName() }}</small>
                                        </td>
                                        <td class="text-center">{{ $result->formatted_training_date }}</td>
                                        <td>{!! Str::limit($result->content, 100) !!}</td>
                                        <td class="text-center">{{ sprintf('%02s', str_replace('.', ',', (float)$result->duration_hours)) }}</td>
                                        <td class="text-center">{{ sprintf('%02d', $result->trung_doi_count) }}</td>
                                        <td class="text-center">{{ sprintf('%02d', $result->at_count + $result->kdt_count) }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $result->result_badge }}">
                                                {{ $result->result_name }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('training-results.show', $result->id) }}"
                                                    class="btn btn-link btn-info btn-lg" data-bs-toggle="tooltip"
                                                    title="Xem chi tiết">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('training-results.edit', $result->id) }}"
                                                    class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip"
                                                    title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('training-results.destroy', $result->id) }}" method="POST"
                                                    id="delete-form-{{ $result->id }}" style="display:inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-link btn-danger"
                                                        data-bs-toggle="tooltip" title="Xóa"
                                                        onclick="confirmDelete({{ $result->id }})">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#training-results-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                },
                "order": [[ 1, "desc" ]],
                "columnDefs": [
                    { "orderable": false, "targets": 7 }
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
                const walk = (x - startX) * 2; // Tốc độ kéo
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
