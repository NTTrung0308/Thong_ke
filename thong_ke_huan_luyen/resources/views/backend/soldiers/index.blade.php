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
        <h3 class="fw-bold mb-3">Quản lý Quân nhân</h3>
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
                <a href="{{ route('soldiers.index') }}">Quân nhân</a>
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
                        <h4 class="card-title">Danh sách quân nhân</h4>
                        <a href="{{ route('soldiers.create') }}" class="btn btn-primary btn-round ms-auto">
                            <i class="fa fa-plus"></i>
                            Thêm mới
                        </a>
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
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endif

                    <div class="table-responsive">
                        <table id="soldiers-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
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
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('soldiers.edit', $soldier->id) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('soldiers.destroy', $soldier->id) }}" method="POST" style="display: inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa quân nhân này?')">
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
            $('#soldiers-datatables').DataTable({
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                },
                "columnDefs": [
                    { "orderable": false, "targets": 13 } // Vô hiệu hóa sắp xếp cho cột Thao tác
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
    </script>
@endsection
