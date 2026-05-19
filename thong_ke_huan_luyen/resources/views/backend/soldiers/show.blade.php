@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Hồ sơ quân nhân điện tử</h3>
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
                <a href="#">{{ $soldier->full_name }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card card-profile">
                <div class="card-header" style="background-image: url('{{ asset('backend/assets/img/blogpost.jpg') }}')">
                    <div class="profile-picture">
                        <div class="avatar avatar-xl">
                            <span class="avatar-title rounded-circle border border-white bg-primary text-white" style="font-size: 2.5rem;">
                                {{ substr($soldier->full_name, strrpos($soldier->full_name, ' ') + 1, 1) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="user-profile text-center">
                        <div class="name fw-bold" style="font-size: 1.25rem;">{{ $soldier->full_name }}</div>
                        <div class="job text-muted mb-2">{{ $soldier->code }}</div>
                        <div class="desc mb-3">
                            <span class="badge badge-primary">{{ $soldier->rank }}</span>
                            <span class="badge badge-info">{{ $soldier->position }}</span>
                        </div>
                        <div class="social-media mb-3">
                            <div class="text-start px-3 small">
                                <p class="mb-1"><strong>Đơn vị:</strong> {{ $soldier->unit ? $soldier->unit->name : 'N/A' }}</p>
                                <p class="mb-1"><strong>Nhập ngũ:</strong> {{ $soldier->enlistment_date ? $soldier->enlistment_date->format('m/Y') : 'N/A' }}</p>
                                <p class="mb-1"><strong>Trình độ:</strong> {{ $soldier->professional_level ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="view-profile">
                            <a href="{{ route('soldiers.edit', $soldier->id) }}" class="btn btn-primary w-100 btn-round">
                                <i class="fa fa-edit me-1"></i> Chỉnh sửa hồ sơ
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row user-stats text-center">
                        <div class="col">
                            <div class="number text-success fw-bold">{{ $soldier->rewards->count() }}</div>
                            <div class="title small text-muted">Khen thưởng</div>
                        </div>
                        <div class="col">
                            <div class="number text-danger fw-bold">{{ $soldier->disciplines->count() }}</div>
                            <div class="title small text-muted">Kỷ luật</div>
                        </div>
                        <div class="col">
                            <div class="number text-info fw-bold">{{ $soldier->trainingLogs->count() }}</div>
                            <div class="title small text-muted">Lần tập huấn</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vũ khí biên chế -->
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title fw-bold"><i class="fas fa-crosshairs me-1 text-danger"></i> Vũ khí & Trang bị</h4>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($soldier->weapons as $weapon)
                            @php
                                $items = [
                                    'AK' => $weapon->ak,
                                    'RPD' => $weapon->rpd,
                                    'B41' => $weapon->b41,
                                    'M79' => $weapon->m79,
                                ];
                            @endphp
                            @foreach ($items as $label => $value)
                                @if ($value)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $label }}
                                        <span class="badge badge-info">{{ $value }}</span>
                                    </li>
                                @endif
                            @endforeach
                        @empty
                            <li class="list-group-item text-center text-muted">Chưa có vũ khí biên chế</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title fw-bold">Chi tiết quá trình công tác & huấn luyện</div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-home-tab-nobd" data-bs-toggle="pill" href="#pills-info" role="tab">Hồ sơ cá nhân</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-training-tab-nobd" data-bs-toggle="pill" href="#pills-training" role="tab">Huấn luyện</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-reward-tab-nobd" data-bs-toggle="pill" href="#pills-timeline" role="tab">Dòng thời gian</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-2 mb-3">
                        <!-- Tab 1: Thông tin chi tiết -->
                        <div class="tab-pane fade show active" id="pills-info" role="tabpanel">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <h5 class="fw-bold p-3 text-white border-0 shadow-sm mb-3" style="background: var(--primary-gradient); border-radius: 8px;">
                                        <i class="fas fa-info-circle me-1"></i> Thông tin cơ bản
                                    </h5>
                                    <div class="row px-2">
                                        <div class="col-6 mb-2"><strong>Ngày sinh:</strong> {{ $soldier->birth_date ? $soldier->birth_date->format('d/m/Y') : 'N/A' }}</div>
                                        <div class="col-6 mb-2"><strong>Hộ khẩu:</strong> {{ $soldier->permanent_residence }}</div>
                                        <div class="col-6 mb-2"><strong>Ngày vào Đảng/Đoàn:</strong> {{ $soldier->party_join_date ? $soldier->party_join_date->format('d/m/Y') : 'Chưa vào' }}</div>
                                        <div class="col-6 mb-2"><strong>Trình độ văn hóa:</strong> {{ $soldier->education }}</div>
                                    </div>


                                    <h5 class="fw-bold p-3 text-white border-0 shadow-sm mb-3 mt-4" style="background: var(--info-gradient); border-radius: 8px;">
                                        <i class="fas fa-phone me-1"></i> Liên lạc khẩn cấp
                                    </h5>
                                    <div class="row px-2">
                                        <div class="col-6 mb-2"><strong>Người báo tin:</strong> {{ $soldier->emergency_contact_name }}</div>
                                        <div class="col-12 mb-2"><strong>Địa chỉ:</strong> {{ $soldier->emergency_contact_address }}</div>
                                    </div>

                                    <h5 class="fw-bold p-3 text-white border-0 shadow-sm mb-3 mt-4" style="background: var(--secondary-gradient); border-radius: 8px;">
                                        <i class="fas fa-sticky-note me-1"></i> Ghi chú
                                    </h5>

                                    <div class="px-2">
                                        <p class="text-muted">{{ $soldier->notes ?? 'Không có ghi chú nào.' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Huấn luyện -->
                        <div class="tab-pane fade" id="pills-training" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Nội dung</th>
                                            <th>Thời gian</th>
                                            <th class="text-center">Xếp loại</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($soldier->trainingLogs as $log)
                                            <tr>
                                                <td class="small">{{ $log->training_date->format('d/m/Y') }}</td>
                                                <td><div class="small fw-bold">{{ $log->training_content ? Str::limit(strip_tags(html_entity_decode($log->training_content)), 50) : '-' }}</div></td>
                                                <td class="text-center">{{ $log->actual_hours }}/{{ $log->required_hours }}h</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $log->rating_badge }}">
                                                        {{ $log->rating_name }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center py-3 text-muted">Chưa có dữ liệu huấn luyện</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: Dòng thời gian -->
                        <div class="tab-pane fade" id="pills-timeline" role="tabpanel">
                            <div class="mt-4 px-3">
                                <ul class="timeline">
                                    @php
                                        $timeline = collect();
                                        foreach($soldier->rewards as $r) {
                                            $timeline->push(['date' => $r->decision_date ?? $r->created_at, 'type' => 'reward', 'data' => $r]);
                                        }
                                        foreach($soldier->disciplines as $d) {
                                            $timeline->push(['date' => $d->decision_date ?? $d->created_at, 'type' => 'discipline', 'data' => $d]);
                                        }
                                        $timeline = $timeline->sortByDesc('date');
                                    @endphp

                                    @forelse($timeline as $item)
                                        <li class="{{ $item['type'] == 'discipline' ? 'timeline-inverted' : '' }}">
                                            <div class="timeline-badge {{ $item['type'] == 'reward' ? 'success' : 'danger' }}">
                                                <i class="fas {{ $item['type'] == 'reward' ? 'fa-medal' : 'fa-exclamation-triangle' }}"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-heading">
                                                    <h4 class="timeline-title fw-bold">
                                                        {{ $item['type'] == 'reward' ? $item['data']->reward_form : $item['data']->discipline_form }}
                                                    </h4>
                                                    <p><small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ $item['date']->format('d/m/Y') }}</small></p>
                                                </div>
                                                <div class="timeline-body">
                                                    <p>{{ $item['type'] == 'reward' ? $item['data']->reason : $item['data']->violation_details }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <p class="text-center text-muted">Chưa có sự kiện quan trọng nào được ghi nhận.</p>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .timeline { list-style: none; padding: 20px 0 20px; position: relative; }
    .timeline:before { top: 0; bottom: 0; position: absolute; content: " "; width: 3px; background-color: #eeeeee; left: 50%; margin-left: -1.5px; }
    .timeline > li { margin-bottom: 20px; position: relative; }
    .timeline > li:before, .timeline > li:after { content: " "; display: table; }
    .timeline > li:after { clear: both; }
    .timeline > li > .timeline-panel { width: 46%; float: left; border: 1px solid #d4d4d4; border-radius: 2px; padding: 15px; position: relative; -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); }
    .timeline > li > .timeline-panel:before { position: absolute; top: 26px; right: -15px; display: inline-block; border-top: 15px solid transparent; border-left: 15px solid #ccc; border-right: 0 solid #ccc; border-bottom: 15px solid transparent; content: " "; }
    .timeline > li > .timeline-panel:after { position: absolute; top: 27px; right: -14px; display: inline-block; border-top: 14px solid transparent; border-left: 14px solid #fff; border-right: 0 solid #fff; border-bottom: 14px solid transparent; content: " "; }
    .timeline > li > .timeline-badge { color: #fff; width: 40px; height: 40px; line-height: 40px; font-size: 1.2em; text-align: center; position: absolute; top: 16px; left: 50%; margin-left: -20px; background-color: #999999; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; }
    .timeline > li.timeline-inverted > .timeline-panel { float: right; }
    .timeline > li.timeline-inverted > .timeline-panel:before { border-left-width: 0; border-right-width: 15px; left: -15px; right: auto; }
    .timeline > li.timeline-inverted > .timeline-panel:after { border-left-width: 0; border-right-width: 14px; left: -14px; right: auto; }
    .timeline-badge.primary { background-color: #2e6da4 !important; }
    .timeline-badge.success { background-color: #3f903f !important; }
    .timeline-badge.warning { background-color: #f0ad4e !important; }
    .timeline-badge.danger { background-color: #d9534f !important; }
    .timeline-badge.info { background-color: #5bc0de !important; }
    @media (max-width: 767px) {
        .timeline:before { left: 40px; }
        .timeline > li > .timeline-panel { width: calc(100% - 90px); width: -moz-calc(100% - 90px); width: -webkit-calc(100% - 90px); }
        .timeline > li > .timeline-badge { left: 15px; margin-left: 0; top: 16px; }
        .timeline > li > .timeline-panel { float: right; }
        .timeline > li > .timeline-panel:before { border-left-width: 0; border-right-width: 15px; left: -15px; right: auto; }
        .timeline > li > .timeline-panel:after { border-left-width: 0; border-right-width: 14px; left: -14px; right: auto; }
    }
</style>
@endpush
