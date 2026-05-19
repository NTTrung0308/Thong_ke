@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Tra cứu thông tin nâng cao</h3>
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
                <a href="#">Tra cứu</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Bộ lọc tìm kiếm</h4>
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                            <i class="fas fa-filter me-1"></i> Ẩn/Hiện bộ lọc
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="collapse show" id="filterCollapse">
                        <form action="{{ route('search.index') }}" method="GET" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Từ khóa</label>
                                <input type="text" name="q" class="form-control"
                                    placeholder="Tên, mã QN, chức vụ, số hiệu súng..." value="{{ request('q') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Đơn vị</label>
                                <select name="unit_id" class="form-select">
                                    <option value="">-- Tất cả đơn vị --</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}"
                                            {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->getFullHierarchyName() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Cấp bậc</label>
                                <select name="rank" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    @foreach ($ranks as $rank)
                                        <option value="{{ $rank }}" {{ request('rank') == $rank ? 'selected' : '' }}>
                                            {{ $rank }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Năm nhập ngũ</label>
                                <select name="enlistment_year" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    @foreach ($enlistmentYears as $year)
                                        <option value="{{ $year }}" {{ request('enlistment_year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Loại vũ khí biên chế</label>
                                <select name="weapon_type" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="ak" {{ request('weapon_type') == 'ak' ? 'selected' : '' }}>Súng tiểu liên AK</option>
                                    <option value="rpd" {{ request('weapon_type') == 'rpd' ? 'selected' : '' }}>Súng trung liên RPD</option>
                                    <option value="b41" {{ request('weapon_type') == 'b41' ? 'selected' : '' }}>Súng diệt tăng B41</option>
                                    <option value="m79" {{ request('weapon_type') == 'm79' ? 'selected' : '' }}>Súng phóng lựu M79</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Trình độ chuyên môn</label>
                                <input type="text" name="professional_level" class="form-control"
                                    placeholder="Nhập trình độ..." value="{{ request('professional_level') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-search me-1"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('search.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </form>
                    </div>

                    <hr>

                    @if (request()->anyFilled(['q', 'unit_id', 'rank', 'enlistment_year', 'weapon_type', 'professional_level']))
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Mã QN</th>
                                        <th>Họ tên</th>
                                        <th>Cấp bậc</th>
                                        <th>Chức vụ</th>
                                        <th>Đơn vị</th>
                                        <th>Trang bị (Số hiệu)</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($soldiers as $soldier)
                                        <tr>
                                            <td><span class="fw-bold">{{ $soldier->code }}</span></td>
                                            <td>{{ $soldier->full_name }}</td>
                                            <td><span class="badge badge-outline-primary">{{ $soldier->rank }}</span></td>
                                            <td>{{ $soldier->position }}</td>
                                            <td>{{ $soldier->unit ? $soldier->unit->name : 'N/A' }}</td>
                                            <td>
                                                @foreach ($soldier->weapons as $weapon)
                                                    @php
                                                        $details = [];
                                                        if ($weapon->ak) $details[] = "AK: $weapon->ak";
                                                        if ($weapon->rpd) $details[] = "RPD: $weapon->rpd";
                                                        if ($weapon->b41) $details[] = "B41: $weapon->b41";
                                                        if ($weapon->m79) $details[] = "M79: $weapon->m79";
                                                    @endphp
                                                    @foreach ($details as $detail)
                                                        <span class="badge badge-info mb-1">{{ $detail }}</span>
                                                    @endforeach
                                                @endforeach
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info btn-quick-view"
                                                        data-id="{{ $soldier->id }}" title="Xem nhanh">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('soldiers.show', $soldier->id) }}"
                                                        class="btn btn-sm btn-primary" title="Hồ sơ chi tiết">
                                                        <i class="fas fa-user-circle"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy kết quả phù hợp</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $soldiers->appends(request()->all())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-search-plus fa-4x text-muted opacity-25"></i>
                            </div>
                            <h5 class="text-muted">Nhập các tiêu chí để bắt đầu tra cứu thông tin quân nhân</h5>
                            <p class="small text-muted">Hệ thống hỗ trợ tìm kiếm theo tên, mã số, đơn vị và các thông tin chuyên sâu.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick View Modal -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Xem nhanh hồ sơ quân nhân</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="quickViewContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Đang tải dữ liệu...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Sử dụng event delegation để bắt sự kiện click cho cả các phần tử được load sau (nếu có)
        $(document).on('click', '.btn-quick-view', function() {
            const soldierId = $(this).data('id');
            const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
            const content = $('#quickViewContent');

            content.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Đang tải dữ liệu...</p></div>');
            modal.show();

            $.ajax({
                url: `{{ url('/search/quick-view') }}/${soldierId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        content.html(response.html);
                    } else {
                        content.html('<div class="alert alert-danger">Không thể tải dữ liệu.</div>');
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    content.html('<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại.</div>');
                }
            });
        });
    });
</script>
@endsection
