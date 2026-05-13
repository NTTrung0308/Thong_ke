@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Hồ sơ quân nhân</h3>
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
                <a href="#">Chi tiết hồ sơ</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Hồ sơ: {{ $soldier->full_name }} ({{ $soldier->code }})</h4>
                        <div class="ms-auto">
                            <a href="{{ route('soldiers.edit', $soldier->id) }}" class="btn btn-primary btn-round me-2">
                                <i class="fa fa-edit"></i> Sửa thông tin
                            </a>
                            <a href="javascript:history.back()" class="btn btn-info btn-round">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-home-tab-nobd" data-bs-toggle="pill"
                                href="#pills-home-nobd" role="tab" aria-controls="pills-home-nobd"
                                aria-selected="true">Thông tin cá nhân</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-weapon-tab-nobd" data-bs-toggle="pill" href="#pills-weapon-nobd"
                                role="tab" aria-controls="pills-weapon-nobd" aria-selected="false">Vũ khí & Trang bị</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-training-tab-nobd" data-bs-toggle="pill"
                                href="#pills-training-nobd" role="tab" aria-controls="pills-training-nobd"
                                aria-selected="false">Lịch sử huấn luyện</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-reward-tab-nobd" data-bs-toggle="pill" href="#pills-reward-nobd"
                                role="tab" aria-controls="pills-reward-nobd" aria-selected="false">Khen thưởng & Kỷ
                                luật</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <!-- Thông tin cá nhân -->
                        <div class="tab-pane fade show active" id="pills-home-nobd" role="tabpanel"
                            aria-labelledby="pills-home-tab-nobd">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%" class="bg-light">Số hiệu quân nhân</th>
                                            <td>{{ $soldier->code }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Họ và tên</th>
                                            <td class="fw-bold">{{ $soldier->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Cấp bậc</th>
                                            <td>{{ $soldier->rank }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Chức vụ</th>
                                            <td>{{ $soldier->position }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Đơn vị</th>
                                            <td>{{ $soldier->unit ? $soldier->unit->getFullHierarchyName() : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Ngày sinh</th>
                                            <td>{{ $soldier->birth_date ? $soldier->birth_date->format('d/m/Y') : '' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Ngày nhập ngũ</th>
                                            <td>{{ $soldier->enlistment_date ? $soldier->enlistment_date->format('m/Y') : '' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Ngày vào Đảng/Đoàn</th>
                                            <td>{{ $soldier->party_join_date ? $soldier->party_join_date->format('d/m/Y') : 'Chưa vào' }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%" class="bg-light">Trình độ văn hóa</th>
                                            <td>{{ $soldier->education }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Ngoại ngữ</th>
                                            <td>{{ $soldier->foreign_language ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Trình độ chuyên môn</th>
                                            <td>{{ $soldier->professional_level ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Người báo tin khi cần</th>
                                            <td>{{ $soldier->emergency_contact_name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Địa chỉ người báo tin</th>
                                            <td>{{ $soldier->emergency_contact_address }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Hộ khẩu thường trú</th>
                                            <td>{{ $soldier->permanent_residence }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Ghi chú</th>
                                            <td>{{ $soldier->notes ?? 'Không có' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Vũ khí & Trang bị -->
                        <div class="tab-pane fade" id="pills-weapon-nobd" role="tabpanel"
                            aria-labelledby="pills-weapon-tab-nobd">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Loại vũ khí/trang bị</th>
                                            <th>Số hiệu/Số lượng</th>
                                            <th>Tình trạng</th>
                                            <th>Ngày biên chế</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $hasWeapons = false; @endphp
                                        @forelse($soldier->weapons as $weapon)
                                            @php
                                                $items = [
                                                    'Súng AK' => $weapon->ak,
                                                    'Súng RPD' => $weapon->rpd,
                                                    'Súng B41' => $weapon->b41,
                                                    'Súng M79' => $weapon->m79,
                                                    'Thông nòng' => $weapon->cleaning_rod,
                                                    'Phụ tùng' => $weapon->spare_parts,
                                                    'Dây súng' => $weapon->gun_strap,
                                                    'Hộp tiếp đạn' => $weapon->magazine_box,
                                                    'Vịt dầu' => $weapon->oil_can,
                                                    'Bao đồ' => $weapon->bag,
                                                    'Áo súng' => $weapon->gun_cover,
                                                    'Bịt nòng' => $weapon->muzzle_cover,
                                                    'Kính ngắm' => $weapon->sight,
                                                    'Lựu đạn' => $weapon->grenade,
                                                    'Xẻng bộ binh' => $weapon->infantry_shovel,
                                                    'Cuốc bộ binh' => $weapon->infantry_pickaxe,
                                                    'Mặt nạ phòng độc' => $weapon->gas_mask,
                                                ];
                                            @endphp
                                            @foreach ($items as $label => $value)
                                                @if ($value)
                                                    @php $hasWeapons = true; @endphp
                                                    <tr>
                                                        <td>{{ $label }}</td>
                                                        <td>{{ $value }}</td>
                                                        <td>
                                                            <span
                                                                class="badge {{ $weapon->status == 'dang-su-dung' ? 'badge-success' : 'badge-warning' }}">
                                                                {{ $weapon->status == 'dang-su-dung' ? 'Đang sử dụng' : 'Khác' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $weapon->created_at->format('d/m/Y') }}</td>
                                                        <td>{{ $weapon->notes }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Chưa có thông tin vũ khí trang bị
                                                </td>
                                            </tr>
                                        @endforelse
                                        @if ($soldier->weapons->count() > 0 && !$hasWeapons)
                                            <tr>
                                                <td colspan="5" class="text-center">Chưa có thông tin vũ khí trang bị
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Lịch sử huấn luyện -->
                        <div class="tab-pane fade" id="pills-training-nobd" role="tabpanel"
                            aria-labelledby="pills-training-tab-nobd">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Nội dung huấn luyện</th>
                                            <th>Thời gian (Giờ)</th>
                                            <th>Kết quả</th>
                                            <th>Đánh giá chung</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($soldier->trainingLogs as $log)
                                            <tr>
                                                <td>{{ $log->training_date->format('d/m/Y') }}</td>
                                                <td>{{ $log->training_content ? Str::limit(strip_tags(html_entity_decode($log->training_content, ENT_QUOTES | ENT_HTML5, 'UTF-8')), 50) : '-' }}</td>
                                                <td>{{ $log->actual_hours }} / {{ $log->required_hours }}</td>
                                                <td>
                                                    <span class="badge {{ $log->rating_badge }}">
                                                        {{ $log->rating_name }}
                                                    </span>
                                                </td>
                                                <td>{{ Str::limit($log->general_evaluation, 50) ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Chưa có dữ liệu huấn luyện cá nhân
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Khen thưởng & Kỷ luật -->
                        <div class="tab-pane fade" id="pills-reward-nobd" role="tabpanel"
                            aria-labelledby="pills-reward-tab-nobd">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h5 class="fw-bold mb-3"><i class="fas fa-medal text-success me-2"></i> Khen thưởng
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Ngày</th>
                                                    <th>Hình thức</th>
                                                    <th>Lý do</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($soldier->rewards as $reward)
                                                    <tr>
                                                        <td>{{ $reward->decision_date ? $reward->decision_date->format('d/m/Y') : '-' }}
                                                        </td>
                                                        <td>{{ $reward->reward_form ? $reward->reward_form : '-' }}</td>
                                                        <td>{{ $reward->reason ? $reward->reason : '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">Chưa có khen
                                                            thưởng</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="fw-bold mb-3"><i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                        Kỷ luật</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Ngày</th>
                                                    <th>Hình thức</th>
                                                    <th>Nội dung</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($soldier->disciplines as $discipline)
                                                    <tr>
                                                        <td>{{ $discipline->decision_date ? $discipline->decision_date->format('d/m/Y') : '-' }}
                                                        </td>
                                                        <td>{{ $discipline->discipline_form ? $discipline->discipline_form : '-' }}</td>
                                                        <td>{{ $discipline->violation_details ? $discipline->violation_details : '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">Chưa có kỷ luật
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
