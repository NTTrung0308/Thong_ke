@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chi tiết Vũ khí - Trang bị</h3>
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
                <a href="{{ route('weapon-equipments.index') }}">Vũ khí trang bị</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Chi tiết</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Thông tin trang bị của: {{ $weaponEquipment->soldier->full_name ?? ($weaponEquipment->soldier_name_at_time ?? 'N/A') }}</h4>
                    <div class="ms-auto">
                        @can('update', $weaponEquipment)
                            <a href="{{ route('weapon-equipments.edit', $weaponEquipment->id) }}" class="btn btn-primary btn-round me-2"><i class="fa fa-edit"></i> Sửa</a>
                        @endcan
                        <a href="{{ route('weapon-equipments.index') }}" class="btn btn-secondary btn-round"><i class="fa fa-arrow-left"></i> Quay lại</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width:35%">Quân nhân</th>
                                    <td>{{ $weaponEquipment->soldier->full_name ?? ($weaponEquipment->soldier_name_at_time ?? 'N/A') }}<br><small>Số hiệu: {{ $weaponEquipment->soldier->code ?? 'N/A' }}</small></td>
                                </tr>
                                <tr>
                                    <th>Đơn vị</th>
                                    <td>{{ $weaponEquipment->unit ? $weaponEquipment->unit->getFullHierarchyName() : ($weaponEquipment->unit_name_at_time ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày nhận</th>
                                    <td>{{ $weaponEquipment->receive_date ? $weaponEquipment->receive_date->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Trạng thái</th>
                                    <td>
                                        @php
                                            $statusLabels = [
                                                'dang-su-dung' => ['label' => 'Đang sử dụng', 'class' => 'badge-primary'],
                                                'da-tra' => ['label' => 'Đã trả', 'class' => 'badge-secondary'],
                                                'dang-bao-duong' => ['label' => 'Đang bảo dưỡng', 'class' => 'badge-info'],
                                            ];
                                            $currentStatus = $statusLabels[$weaponEquipment->status] ?? ['label' => $weaponEquipment->status, 'class' => 'badge-dark'];
                                        @endphp
                                        <span class="badge {{ $currentStatus['class'] }}">{{ $currentStatus['label'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tình trạng kỹ thuật</th>
                                    <td>
                                        @php
                                            $conditionLabels = [
                                                'tot' => ['label' => 'Tốt', 'class' => 'badge-success'],
                                                'hỏng' => ['label' => 'Hỏng', 'class' => 'badge-danger'],
                                                'cần_bảo_dưỡng' => ['label' => 'Cần bảo dưỡng', 'class' => 'badge-warning'],
                                            ];
                                            $currentCondition = $conditionLabels[$weaponEquipment->condition] ?? ['label' => $weaponEquipment->condition, 'class' => 'badge-dark'];
                                        @endphp
                                        <span class="badge {{ $currentCondition['class'] }}">{{ $currentCondition['label'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ghi chú</th>
                                    <td>{{ $weaponEquipment->notes ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Danh sách trang bị cụ thể</h5>
                            <div class="row">
                                <div class="col-6">
                                    <ul class="list-group list-group-bordered">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">AK <span>{{ $weaponEquipment->ak ?? '-' }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">RPD <span>{{ $weaponEquipment->rpd ?? '-' }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">B41 <span>{{ $weaponEquipment->b41 ?? '-' }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">M79 <span>{{ $weaponEquipment->m79 ?? '-' }}</span></li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul class="list-group list-group-bordered">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">Lựu đạn <span>{{ $weaponEquipment->grenade ?? '0' }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">Thông nòng <span>{{ $weaponEquipment->cleaning_rod ?? '-' }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">Phụ tùng <span>{{ $weaponEquipment->spare_parts ?? '-' }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">Kính ngắm <span>{{ $weaponEquipment->sight ?? '-' }}</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($weaponEquipment->history)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="fw-bold mb-2"><i class="fas fa-history me-1"></i> Lịch sử thay đổi</h5>
                                <div class="bg-light p-3 rounded" style="white-space: pre-line; font-family: monospace; max-height: 200px; overflow-y: auto;">{{ $weaponEquipment->history }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-12 text-end text-muted small">
                            Được tạo bởi: {{ $weaponEquipment->creator->name ?? 'N/A' }} lúc {{ $weaponEquipment->created_at ? $weaponEquipment->created_at->format('H:i d/m/Y') : '' }}
                            @if($weaponEquipment->updated_by)
                                | Cập nhật: {{ $weaponEquipment->updater->name ?? 'N/A' }} lúc {{ $weaponEquipment->updated_at ? $weaponEquipment->updated_at->format('H:i d/m/Y') : '' }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
