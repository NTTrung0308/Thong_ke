@extends('backend.layouts.dashboard')

@php
use Illuminate\Support\Str;
@endphp

@section('dashboard_content')
    <style>
        .table-responsive {
            cursor: grab;
        }
        .table-responsive.dragging {
            cursor: grabbing;
            user-select: none;
        }
        #soldiers-search-datatables th, #soldiers-search-datatables td,
        #weapon-search-datatables th, #weapon-search-datatables td,
        #rewards-search-datatables th, #rewards-search-datatables td,
        #disciplines-search-datatables th, #disciplines-search-datatables td,
        #training-results-search-datatables th, #training-results-search-datatables td,
        #training-logs-search-datatables th, #training-logs-search-datatables td {
            white-space: nowrap;
            vertical-align: middle;
        }

        /* Highlight action icons specifically inside the soldiers search results */
        #soldiers-search-datatables .btn-link {
            color: rgba(58,66,86,0.95) !important;
            opacity: 1 !important;
            padding: 6px !important;
            border-radius: 6px !important;
            transition: all 0.12s ease;
        }
        #soldiers-search-datatables .btn-link .fa, #soldiers-search-datatables .btn-link .fas, #soldiers-search-datatables .btn-link .far {
            font-size: 1.08rem !important;
        }
        /* Specific semantic colors for quick-view (eye), profile and edit */
        #soldiers-search-datatables .btn-link.btn-info {
            color: #0d6efd !important; /* bright blue */
            background: rgba(13,110,253,0.08) !important;
        }
        #soldiers-search-datatables .btn-link.btn-primary {
            color: #0b5ed7 !important; /* darker blue for profile */
            background: rgba(11,94,215,0.08) !important;
        }
        #soldiers-search-datatables .btn-link.btn-warning {
            color: #ff8c1a !important; /* clearer orange for edit */
            background: rgba(255,140,26,0.08) !important;
        }
        /* If delete appears in search results */
        #soldiers-search-datatables .btn-link.btn-danger {
            color: #dc3545 !important; /* clear red */
            background: rgba(220,53,69,0.08) !important;
        }
        #soldiers-search-datatables .btn-link:hover {
            text-decoration: none !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.06);
        }
    </style>
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
                            @if(!in_array(request('module', 'soldiers'), ['training_results', 'soldiers']))
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Từ khóa</label>
                                    <input type="text" name="q" class="form-control"
                                        placeholder="Nhập từ khóa" value="{{ request('q') }}">
                                </div>
                            @endif
                            <div class="col-md-3">
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
                                <div class="col-md-5">
                                    <label class="form-label fw-bold">Tìm kiếm tổng hợp</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
                                        <input type="text" name="q" class="form-control border-start-0" 
                                            placeholder="Nhập tên, số hiệu, học vấn, trình độ, ngày sinh (dd/mm/yyyy)..." 
                                            value="{{ request('q') }}">
                                    </div>
                                    <small class="text-muted">Ví dụ: "Đại học", "Binh nhất", "20/05/2000", "Súng AK"...</small>
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
                            @endif

                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-search me-1"></i> Tìm
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
                            <table id="rewards-search-datatables" class="display table table-striped table-hover table-bordered">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th>STT</th>
                                        <th>Đối tượng (Quân nhân/Đơn vị)</th>
                                        <th>Lý do khen thưởng</th>
                                        <th>Hình thức & Quyết định</th>
                                        <th style="width: 10%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rewards as $reward)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + ($rewards->currentPage()-1)*$rewards->perPage() }}</td>
                                            <td>
                                                @if($reward->soldier)
                                                    <strong>{{ $reward->soldier->full_name }}</strong><br>
                                                    <small class="text-muted">ĐV: {{ $reward->soldier->unit->name ?? 'N/A' }}</small>
                                                @else
                                                    <strong>{{ $reward->unit->name ?? $reward->unit_name_at_time }}</strong><br>
                                                    <small class="text-muted">(Khen thưởng tập thể)</small>
                                                @endif
                                            </td>
                                            <td>{{ $reward->reason ?? '-' }}</td>
                                            <td>
                                                @if($reward->reward_form)
                                                    <strong>{{ $reward->reward_form }}</strong><br>
                                                    <small>
                                                        Ngày: {{ $reward->decision_date ? $reward->decision_date->format('d/m/Y') : '...' }}<br>
                                                        Cấp: {{ $reward->decision_level }} (Số: {{ $reward->decision_number ?? '...' }})
                                                    </small>
                                                @else
                                                    <span class="text-muted">Chưa cập nhật</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
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
                                                </div>
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
                            <table id="disciplines-search-datatables" class="display table table-striped table-hover table-bordered">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th>STT</th>
                                        <th>Đối tượng (Quân nhân/Đơn vị)</th>
                                        <th>Nội dung vi phạm</th>
                                        <th>Hình thức & Quyết định</th>
                                        <th>Trạng thái</th>
                                        <th style="width: 10%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($disciplines as $discipline)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + ($disciplines->currentPage()-1)*$disciplines->perPage() }}</td>
                                            <td>
                                                @if($discipline->soldier)
                                                    <strong>{{ $discipline->soldier->full_name }}</strong><br>
                                                    <small class="text-muted">ĐV: {{ $discipline->soldier->unit->name ?? 'N/A' }}</small>
                                                @else
                                                    <strong>{{ $discipline->unit->name ?? $discipline->unit_name_at_time }}</strong><br>
                                                    <small class="text-muted">(Kỷ luật tập thể)</small>
                                                @endif
                                            </td>
                                            <td>{!! $discipline->violation_details ? Str::limit(strip_tags($discipline->violation_details), 80) : '-' !!}</td>
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
                                            <td class="text-center">
                                                @if($discipline->discipline_form)
                                                    <span class="badge {{ $discipline->status_badge }}">
                                                        {{ $discipline->status_name }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
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
                                                </div>
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
                            <table id="training-results-search-datatables" class="display table table-striped table-hover table-bordered">
                                <thead class="bg-light text-center">
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
                                    @forelse($trainingResults as $tr)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + ($trainingResults->currentPage()-1)*$trainingResults->perPage() }}</td>
                                            <td>
                                                <strong>{{ $tr->unit->name ?? $tr->unit_name_at_time }}</strong><br>
                                                <small class="text-muted">{{ $tr->unit ? $tr->unit->getFullHierarchyName() : 'N/A' }}</small>
                                            </td>
                                            <td class="text-center">{{ $tr->training_date ? $tr->training_date->format('d/m/Y') : '' }}</td>
                                            <td>{!! Str::limit(strip_tags($tr->content), 100) !!}</td>
                                            <td class="text-center">{{ sprintf('%02s', str_replace('.', ',', (float)$tr->duration_hours)) }}</td>
                                            <td class="text-center">{{ sprintf('%02d', $tr->trung_doi_count) }}</td>
                                            <td class="text-center">{{ sprintf('%02d', $tr->at_count + $tr->kdt_count) }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $tr->result_badge }}">
                                                    {{ $tr->result_name }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-button-action">
                                                    <a href="{{ route('training-results.show', $tr->id) }}"
                                                        class="btn btn-link btn-info btn-lg" data-bs-toggle="tooltip"
                                                        title="Xem chi tiết">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('training-results.edit', $tr->id) }}"
                                                        class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip"
                                                        title="Sửa">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center text-muted py-4">Không tìm thấy kết quả tập huấn phù hợp</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $trainingResults->appends(request()->all())->links() }}</div>

                    @elseif(request('module') == 'training_logs')
                        <div class="table-responsive">
                            <table id="training-logs-search-datatables" class="display table table-striped table-hover table-bordered">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th rowspan="3">STT</th>
                                        <th rowspan="3">Đối tượng (Quân nhân/Đơn vị)</th>
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
                                    @forelse($trainingLogs as $log)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + ($trainingLogs->currentPage()-1)*$trainingLogs->perPage() }}</td>
                                            <td>
                                                @if ($log->soldier)
                                                    <strong>{{ $log->soldier->full_name }}</strong><br>
                                                    <small class="text-muted">ĐV: {{ $log->soldier->unit->name ?? 'N/A' }}</small>
                                                @else
                                                    <strong>{{ $log->unit_name_at_time }}</strong><br>
                                                    <small class="text-muted">(Đơn vị)</small>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $log->attendance_mon }}</td>
                                            <td class="text-center">{{ $log->attendance_tue }}</td>
                                            <td class="text-center">{{ $log->attendance_wed }}</td>
                                            <td class="text-center">{{ $log->attendance_thu }}</td>
                                            <td class="text-center">{{ $log->attendance_fri }}</td>
                                            <td class="text-center">{{ $log->attendance_sat }}</td>
                                            <td class="text-center">{{ $log->attendance_sun }}</td>
                                            <td class="text-center">
                                                @if ($log->training_date)
                                                    {{ $log->day_of_week }}, {{ $log->training_date->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-start">
                                                {{ $log->training_content ? Str::limit(strip_tags(html_entity_decode($log->training_content, ENT_QUOTES | ENT_HTML5, 'UTF-8')), 50) : '-' }}
                                            </td>
                                            <td class="text-center">{{ $log->required_quanso }}</td>
                                            <td class="text-center">{{ $log->actual_quanso }}</td>
                                            <td class="text-center">{{ $log->required_hours }}</td>
                                            <td class="text-center">{{ $log->actual_hours }}</td>
                                            <td class="text-center">{{ $log->test_quanso }}</td>
                                            <td class="text-center">{{ $log->good_count }}</td>
                                            <td class="text-center">{{ $log->good_percent }}</td>
                                            <td class="text-center">{{ $log->fair_count }}</td>
                                            <td class="text-center">{{ $log->fair_percent }}</td>
                                            <td class="text-center">{{ $log->pass_count }}</td>
                                            <td class="text-center">{{ $log->pass_percent }}</td>
                                            <td class="text-center">{{ $log->fail_count }}</td>
                                            <td class="text-center">{{ $log->fail_percent }}</td>
                                            <td class="text-center">
                                                @if ($log->training_content)
                                                    <span class="badge {{ $log->rating_badge }}">
                                                        {{ $log->rating_name }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
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
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="26" class="text-center text-muted py-4">Không tìm thấy nhật ký huấn luyện phù hợp</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $trainingLogs->appends(request()->all())->links() }}</div>

                    @elseif(request('module') == 'weapon_equipments')
                        <div class="table-responsive">
                            <table id="weapon-search-datatables" class="display table table-striped table-hover table-bordered">
                                <thead class="bg-light text-center">
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
                                        <th style="width: 5%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($equipments as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + ($equipments->currentPage()-1)*$equipments->perPage() }}</td>
                                            <td class="fw-bold">
                                                {{ $item->soldier ? $item->soldier->full_name : 'N/A' }}
                                            </td>
                                            <td class="text-center">{{ $item->ak ?? '-' }}</td>
                                            <td class="text-center">{{ $item->rpd ?? '-' }}</td>
                                            <td class="text-center">{{ $item->b41 ?? '-' }}</td>
                                            <td class="text-center">{{ $item->m79 ?? '-' }}</td>
                                            <td class="text-center">{{ $item->cleaning_rod ?? '-' }}</td>
                                            <td class="text-center">{{ $item->spare_parts ?? '-' }}</td>
                                            <td class="text-center">{{ $item->gun_strap ?? '-' }}</td>
                                            <td class="text-center">{{ $item->magazine_box ?? '-' }}</td>
                                            <td class="text-center">{{ $item->oil_can ?? '-' }}</td>
                                            <td class="text-center">{{ $item->bag ?? '-' }}</td>
                                            <td class="text-center">{{ $item->gun_cover ?? '-' }}</td>
                                            <td class="text-center">{{ $item->muzzle_cover ?? '-' }}</td>
                                            <td class="text-center">{{ $item->sight ?? '-' }}</td>
                                            <td class="text-center">{{ $item->grenade ?? '-' }}</td>
                                            <td class="text-center">{{ $item->infantry_shovel ?? '-' }}</td>
                                            <td class="text-center">{{ $item->infantry_pickaxe ?? '-' }}</td>
                                            <td class="text-center">
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
                                            <td class="text-center small">
                                                {{ $item->receive_date ? $item->receive_date->format('d/m/Y') : '' }}
                                                @if ($item->received_by)
                                                    <br>({{ $item->received_by }})
                                                @endif
                                            </td>
                                            <td class="text-center small">
                                                {{ $item->return_date ? $item->return_date->format('d/m/Y') : '' }}
                                                @if ($item->returned_by)
                                                    <br>({{ $item->returned_by }})
                                                @endif
                                            </td>
                                            <td>{{ $item->notes }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('weapon-equipments.edit', $item->id) }}" class="btn btn-link btn-primary p-2" title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="23" class="text-center text-muted py-4">Không tìm thấy trang bị phù hợp</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $equipments->appends(request()->all())->links() }}</div>

                    @else
                        @if(isset($soldiers) && $soldiers->count() > 0)
                            <div class="table-responsive">
                                <table id="soldiers-search-datatables" class="display table table-striped table-hover table-bordered">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>STT</th>
                                            <th>Họ và tên</th>
                                            <th>Cấp bậc</th>
                                            <th>Chức vụ</th>
                                            <th>Đơn vị</th>
                                            <th>Ngày sinh</th>
                                            <th>Nhập ngũ</th>
                                            <th>Số hiệu QN</th>
                                            <th>Đảng/Đoàn</th>
                                            <th>Học vấn/Ngoại ngữ</th>
                                            <th>Nghề nghiệp/Chuyên môn</th>
                                            <th>Hộ khẩu thường trú</th>
                                            <th>Liên hệ khẩn cấp</th>
                                            <th>Ghi chú</th>
                                            <th style="width: 10%">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($soldiers as $index => $soldier)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration + ($soldiers->currentPage()-1)*$soldiers->perPage() }}</td>
                                                <td class="fw-bold">{{ $soldier->full_name }}</td>
                                                <td class="text-center">{{ $soldier->rank }}</td>
                                                <td>{{ $soldier->position }}</td>
                                                <td>{{ $soldier->unit ? $soldier->unit->name : 'N/A' }}</td>
                                                <td class="text-center">{{ $soldier->birth_date ? $soldier->birth_date->format('d/m/Y') : '' }}</td>
                                                <td class="text-center">{{ $soldier->enlistment_date ? $soldier->enlistment_date->format('m/Y') : '' }}</td>
                                                <td class="text-center fw-bold">{{ $soldier->code }}</td>
                                                <td class="text-center">{{ $soldier->party_join_date ? $soldier->party_join_date->format('d/m/Y') : '' }}</td>
                                                <td>{{ $soldier->education }} / {{ $soldier->foreign_language }}</td>
                                                <td>{{ $soldier->professional_level }}</td>
                                                <td>{{ $soldier->permanent_residence }}</td>
                                                <td>{{ $soldier->emergency_contact_name }} - {{ $soldier->emergency_contact_address }}</td>
                                                <td>{{ $soldier->notes }}</td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-link btn-info btn-lg btn-quick-view p-2"
                                                            data-id="{{ $soldier->id }}" title="Xem nhanh">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <a href="{{ route('soldiers.show', $soldier->id) }}"
                                                            class="btn btn-link btn-primary btn-lg p-2" title="Hồ sơ chi tiết">
                                                            <i class="fas fa-user-circle"></i>
                                                        </a>
                                                        <a href="{{ route('soldiers.edit', $soldier->id) }}" class="btn btn-link btn-warning btn-lg p-2" title="Sửa">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
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

        // Kéo bảng sang ngang bằng chuột
        const slider = document.querySelector('.table-responsive');
        if (slider) {
            let isDown = false;
            let startX;
            let scrollLeft;

            slider.addEventListener('mousedown', (e) => {
                if (e.button !== 0 || e.target.closest('a, button')) return;
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
</script>
@endsection
