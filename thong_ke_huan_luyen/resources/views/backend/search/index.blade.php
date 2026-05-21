@extends('backend.layouts.dashboard')

@php
use Illuminate\Support\Str;
@endphp

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
                                <input type="hidden" name="module" value="{{ request('module', 'soldiers') }}">
                            @if(request('module') != 'training_results')
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Từ khóa</label>
                                    <input type="text" name="q" class="form-control"
                                        placeholder="Nhập từ khóa" value="{{ request('q') }}">
                                </div>
                            @endif
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

                            {{-- Module-specific filters --}}
                            @if(request('module') == 'rewards')
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Năm quyết định</label>
                                    <input type="number" name="year" class="form-control" value="{{ request('year') }}" placeholder="YYYY">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Cấp quyết định</label>
                                    <input type="text" name="decision_level" class="form-control" value="{{ request('decision_level') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Loại</label>
                                    <select name="type" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="unit" {{ request('type')=='unit' ? 'selected' : '' }}>Khen thưởng đơn vị</option>
                                        <option value="superior" {{ request('type')=='superior' ? 'selected' : '' }}>Khen thưởng cấp trên</option>
                                    </select>
                                </div>
                            @elseif(request('module') == 'disciplines')
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Trạng thái</label>
                                    <select name="status" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="dang-thi-hanh" {{ request('status')=='dang-thi-hanh' ? 'selected' : '' }}>Đang thi hành</option>
                                        <option value="da-thi-hanh-xong" {{ request('status')=='da-thi-hanh-xong' ? 'selected' : '' }}>Đã thi hành xong</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Năm quyết định</label>
                                    <input type="number" name="year" class="form-control" value="{{ request('year') }}" placeholder="YYYY">
                                </div>
                            @elseif(request('module') == 'training_results')
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Năm</label>
                                    <input type="number" name="year" class="form-control" value="{{ request('year') }}" placeholder="YYYY">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Tháng</label>
                                    <input type="number" name="month" class="form-control" value="{{ request('month') }}" placeholder="MM">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Kết quả</label>
                                    <select name="result" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="xuất_sắc" {{ request('result')=='xuất_sắc' ? 'selected' : '' }}>Xuất sắc</option>
                                        <option value="giỏi" {{ request('result')=='giỏi' ? 'selected' : '' }}>Giỏi</option>
                                        <option value="khá" {{ request('result')=='khá' ? 'selected' : '' }}>Khá</option>
                                        <option value="trung_bình" {{ request('result')=='trung_bình' ? 'selected' : '' }}>Trung bình</option>
                                    </select>
                                </div>
                            @elseif(request('module') == 'training_logs')
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Từ ngày</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Đến ngày</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Xếp loại</label>
                                    <select name="rating" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="xuất_sắc" {{ request('rating')=='xuất_sắc' ? 'selected' : '' }}>Xuất sắc</option>
                                        <option value="giỏi" {{ request('rating')=='giỏi' ? 'selected' : '' }}>Giỏi</option>
                                        <option value="khá" {{ request('rating')=='khá' ? 'selected' : '' }}>Khá</option>
                                        <option value="trung_bình" {{ request('rating')=='trung_bình' ? 'selected' : '' }}>Trung bình</option>
                                        <option value="yếu" {{ request('rating')=='yếu' ? 'selected' : '' }}>Yếu</option>
                                    </select>
                                </div>
                            @elseif(request('module') == 'weapon_equipments')
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
                            @else
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
                                    <label class="form-label fw-bold">Trình độ chuyên môn</label>
                                    <input type="text" name="professional_level" class="form-control"
                                        placeholder="Nhập trình độ..." value="{{ request('professional_level') }}">
                                </div>
                            @endif

                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-search me-1"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('search.index', ['module' => request('module')]) }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </form>
                    </div>

                    <hr>

                    @if(request('module') == 'rewards')
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Quân nhân/Đơn vị</th>
                                        <th>Lý do</th>
                                        <th>Hình thức & Quyết định</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rewards as $reward)
                                        <tr>
                                            <td>{{ $loop->iteration + ($rewards->currentPage()-1)*$rewards->perPage() }}</td>
                                            <td>
                                                @if($reward->soldier)
                                                    <strong>{{ $reward->soldier->full_name }}</strong><br>
                                                    <small class="text-muted">ĐV: {{ $reward->soldier->unit->name ?? 'N/A' }}</small>
                                                @else
                                                    <strong>{{ $reward->unit->name ?? $reward->unit_name_at_time }}</strong>
                                                @endif
                                            </td>
                                            <td>{{ $reward->reason ?? '-' }}</td>
                                            <td>
                                                <strong>{{ $reward->reward_form }}</strong><br>
                                                <small>Ngày: {{ $reward->decision_date ? $reward->decision_date->format('d/m/Y') : '...' }}<br>Cấp: {{ $reward->decision_level }}</small>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('rewards.show', $reward->id) }}" class="btn btn-sm btn-info">Xem</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">Không tìm thấy khen thưởng phù hợp</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $rewards->appends(request()->all())->links() }}</div>

                    @elseif(request('module') == 'disciplines')
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Quân nhân/Đơn vị</th>
                                        <th>Vi phạm</th>
                                        <th>Trạng thái</th>
                                        <th>Quyết định</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($disciplines as $d)
                                        <tr>
                                            <td>{{ $loop->iteration + ($disciplines->currentPage()-1)*$disciplines->perPage() }}</td>
                                            <td>{{ $d->soldier_name_at_time ?? $d->unit_name_at_time }}</td>
                                            <td>{!! Str::limit($d->violation_details, 80) !!}</td>
                                            <td><span class="badge {{ $d->getStatusBadgeAttribute() }}">{{ $d->getStatusNameAttribute() }}</span></td>
                                            <td>{{ $d->decision_date ? $d->decision_date->format('d/m/Y') : '' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('disciplines.show', $d->id) }}" class="btn btn-sm btn-info">Xem</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-4">Không tìm thấy kỷ luật phù hợp</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $disciplines->appends(request()->all())->links() }}</div>

                    @elseif(request('module') == 'training_results')
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Đơn vị</th>
                                        <th>Ngày</th>
                                        <th>Nội dung</th>
                                        <th>Kết quả</th>
                                            <th class="text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($trainingResults as $tr)
                                            <tr>
                                                <td>{{ $loop->iteration + ($trainingResults->currentPage()-1)*$trainingResults->perPage() }}</td>
                                                <td>{{ $tr->unit->name ?? $tr->unit_name_at_time }}</td>
                                                <td>{{ $tr->training_date ? $tr->training_date->format('d/m/Y') : '' }}</td>
                                                <td>{{ Str::limit($tr->content, 80) }}</td>
                                                <td><span class="badge {{ $tr->getResultBadgeAttribute() }}">{{ $tr->getResultNameAttribute() }}</span></td>
                                                <td class="text-center">
                                                    <a href="{{ route('training-results.show', $tr->id) }}" class="btn btn-sm btn-info">Xem</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted py-4">Không tìm thấy kết quả tập huấn phù hợp</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $trainingResults->appends(request()->all())->links() }}</div>

                    @elseif(request('module') == 'training_logs')
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Ngày</th>
                                        <th>Quân nhân</th>
                                        <th>Đơn vị</th>
                                        <th>Nội dung</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($trainingLogs as $log)
                                        <tr>
                                            <td>{{ $loop->iteration + ($trainingLogs->currentPage()-1)*$trainingLogs->perPage() }}</td>
                                            <td>{{ $log->training_date ? $log->training_date->format('d/m/Y') : '' }}</td>
                                            <td>{{ $log->soldier_name_at_time }}</td>
                                            <td>{{ $log->unit->name ?? $log->unit_name_at_time }}</td>
                                            <td>{!! Str::limit($log->training_content, 100) !!}</td>
                                            <td class="text-center">
                                                <a href="{{ route('training-logs.show', $log->id) }}" class="btn btn-sm btn-info">Xem</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-4">Không tìm thấy nhật ký huấn luyện phù hợp</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $trainingLogs->appends(request()->all())->links() }}</div>

                    @elseif(request('module') == 'weapon_equipments')
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Quân nhân</th>
                                        <th>Vũ khí</th>
                                        <th>Tình trạng</th>
                                        <th>Ngày nhận</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($equipments as $item)
                                        <tr>
                                            <td>{{ $loop->iteration + ($equipments->currentPage()-1)*$equipments->perPage() }}</td>
                                            <td>{{ $item->soldier ? $item->soldier->full_name : 'N/A' }}</td>
                                            <td>{{ $item->weapons_list ?? '' }}</td>
                                            <td>{{ $item->status }}</td>
                                            <td>{{ $item->receive_date ? $item->receive_date->format('d/m/Y') : '' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">Không tìm thấy trang bị phù hợp</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $equipments->appends(request()->all())->links() }}</div>

                    @else
                        @if(isset($soldiers) && $soldiers->count() > 0)
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
