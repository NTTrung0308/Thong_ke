@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Nhật ký hoạt động hệ thống</h3>
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
                <a href="#">Hệ thống</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('activity-logs.index') }}">Nhật ký hoạt động</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc tìm kiếm</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('activity-logs.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Hành động</label>
                                    <select name="event" class="form-select form-control">
                                        <option value="">-- Tất cả --</option>
                                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Thêm mới</option>
                                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Cập nhật</option>
                                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Xóa</option>
                                        <option value="restored" {{ request('event') == 'restored' ? 'selected' : '' }}>Khôi phục</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Loại đối tượng</label>
                                    <select name="subject_type" class="form-select form-control">
                                        <option value="">-- Tất cả --</option>
                                        @php
                                            $modelNames = [
                                                'Soldier' => 'Quân nhân',
                                                'Unit' => 'Đơn vị',
                                                'User' => 'Người dùng',
                                                'TrainingLog' => 'Nhật ký huấn luyện',
                                                'TrainingResult' => 'Kết quả tập huấn',
                                                'WeaponEquipment' => 'Vũ khí trang bị',
                                                'Reward' => 'Khen thưởng',
                                                'Discipline' => 'Kỷ luật'
                                            ];
                                        @endphp
                                        @foreach($subjectTypes as $type)
                                            <option value="{{ $type }}" {{ request('subject_type') == $type ? 'selected' : '' }}>
                                                {{ $modelNames[$type] ?? $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Người thực hiện</label>
                                    <select name="causer_id" class="form-select form-control">
                                        <option value="">-- Tất cả --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Từ ngày</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Đến ngày</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Lọc
                                    </button>
                                    <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> Làm mới
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lịch sử thay đổi dữ liệu</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Người thực hiện</th>
                                    <th>Hành động</th>
                                    <th>Đối tượng</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $fieldLabels = [
                                        'full_name' => 'Họ và tên',
                                        'code' => 'Số hiệu',
                                        'rank' => 'Cấp bậc',
                                        'position' => 'Chức vụ',
                                        'unit_id' => 'Đơn vị',
                                        'birth_date' => 'Ngày sinh',
                                        'enlistment_date' => 'Ngày nhập ngũ',
                                        'party_join_date' => 'Ngày vào Đảng',
                                        'education' => 'Học vấn',
                                        'foreign_language' => 'Ngoại ngữ',
                                        'professional_level' => 'Trình độ chuyên môn',
                                        'permanent_residence' => 'Hộ khẩu thường trú',
                                        'emergency_contact_name' => 'Người liên hệ khẩn cấp',
                                        'emergency_contact_address' => 'Địa chỉ khẩn cấp',
                                        'name' => 'Tên',
                                        'level' => 'Cấp',
                                        'parent_id' => 'Đơn vị cha',
                                        'reason' => 'Lý do',
                                        'reward_form' => 'Hình thức khen thưởng',
                                        'discipline_form' => 'Hình thức kỷ luật',
                                        'status' => 'Trạng thái',
                                        'notes' => 'Ghi chú',
                                        'content' => 'Nội dung',
                                        'training_date' => 'Ngày huấn luyện',
                                        'result' => 'Kết quả',
                                        'email' => 'Email',
                                        'password' => 'Mật khẩu',
                                        'ak' => 'Súng AK',
                                        'rpd' => 'Súng RPD',
                                        'b41' => 'Súng B41',
                                        'm79' => 'Súng M79',
                                        'grenade' => 'Lựu đạn',
                                        'instructor' => 'Người hướng dẫn',
                                        'commander' => 'Chỉ huy',
                                        'supervisor' => 'Người giám sát',
                                        'evaluation' => 'Đánh giá chung'
                                    ];
                                @endphp
                                @foreach($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            @if($activity->causer)
                                                {{ $activity->causer->name }}
                                            @else
                                                <span class="text-muted">Hệ thống</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badges = [
                                                    'created' => 'badge-success',
                                                    'updated' => 'badge-primary',
                                                    'deleted' => 'badge-danger',
                                                    'restored' => 'badge-info'
                                                ];
                                                $eventNames = [
                                                    'created' => 'Thêm mới',
                                                    'updated' => 'Cập nhật',
                                                    'deleted' => 'Xóa',
                                                    'restored' => 'Khôi phục'
                                                ];
                                            @endphp
                                            <span class="badge {{ $badges[$activity->event] ?? 'badge-secondary' }}">
                                                {{ $eventNames[$activity->event] ?? $activity->event }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $subjectType = str_replace('App\\Models\\', '', $activity->subject_type);
                                            @endphp
                                            {{ $modelNames[$subjectType] ?? $subjectType }}
                                            @if($activity->subject)
                                                <small class="text-muted d-block">ID: {{ $activity->subject_id }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($activity->event == 'updated')
                                                @php
                                                    $old = $activity->properties['old'] ?? [];
                                                    $attributes = $activity->properties['attributes'] ?? [];
                                                    $changes = [];
                                                    foreach($attributes as $key => $value) {
                                                        if(isset($old[$key]) && $old[$key] != $value) {
                                                            $label = $fieldLabels[$key] ?? $key;
                                                            $changes[] = "<strong>$label</strong>: " . ($old[$key] ?? 'N/A') . " <i class='fas fa-arrow-right mx-1'></i> " . ($value ?? 'N/A');
                                                        }
                                                    }
                                                @endphp
                                                {!! implode('<br>', $changes) !!}
                                            @elseif($activity->event == 'created')
                                                <span class="text-success">Dữ liệu mới đã được tạo.</span>
                                            @elseif($activity->event == 'deleted')
                                                <span class="text-danger">Dữ liệu đã bị xóa.</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
