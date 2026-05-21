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
                                    <td>{{ $weaponEquipment->status }}</td>
                                </tr>
                                <tr>
                                    <th>Ghi chú</th>
                                    <td>{{ $weaponEquipment->notes ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Danh sách trang bị</h5>
                            <ul class="list-group">
                                @if($weaponEquipment->ak)
                                    <li class="list-group-item">AK: {{ $weaponEquipment->ak }}</li>
                                @endif
                                @if($weaponEquipment->rpd)
                                    <li class="list-group-item">RPD: {{ $weaponEquipment->rpd }}</li>
                                @endif
                                @if($weaponEquipment->b41)
                                    <li class="list-group-item">B41: {{ $weaponEquipment->b41 }}</li>
                                @endif
                                @if($weaponEquipment->m79)
                                    <li class="list-group-item">M79: {{ $weaponEquipment->m79 }}</li>
                                @endif
                                @if($weaponEquipment->grenade)
                                    <li class="list-group-item">Lựu đạn: {{ $weaponEquipment->grenade }}</li>
                                @endif
                                @if(!$weaponEquipment->ak && !$weaponEquipment->rpd && !$weaponEquipment->b41 && !$weaponEquipment->m79 && !$weaponEquipment->grenade)
                                    <li class="list-group-item text-muted">Không có trang bị cụ thể</li>
                                @endif
                            </ul>
                        </div>
                    </div>

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
