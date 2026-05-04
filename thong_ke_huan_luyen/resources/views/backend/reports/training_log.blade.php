@extends('backend.layouts.dashboard')

@section('dashboard_content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Báo cáo Nhật ký huấn luyện</h3>
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
            <a href="{{ route('training-logs.index') }}">Nhật ký huấn luyện</a>
        </li>
        <li class="separator">
            <i class="fas fa-chevron-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Báo cáo</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <form action="{{ route('training-logs.report') }}" method="GET" class="row">
                    <div class="col-md-3">
                        <div class="form-group p-0">
                            <label>Đơn vị</label>
                            <select name="unit_id" class="form-control">
                                <option value="">Tất cả đơn vị</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group p-0">
                            <label>Tháng</label>
                            <select name="month" class="form-control">
                                @foreach($months as $m)
                                    <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group p-0">
                            <label>Năm</label>
                            <select name="year" class="form-control">
                                @if($years->isEmpty())
                                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                @else
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-filter"></i> Lọc
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Số ngày HL</p>
                                            <h4 class="card-title">{{ $summary['total_days'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-info bubble-shadow-small">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Tỉ lệ tham gia</p>
                                            <h4 class="card-title">{{ $summary['attendance_rate'] }}%</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-success bubble-shadow-small">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Tỉ lệ thời gian</p>
                                            <h4 class="card-title">{{ $summary['time_rate'] }}%</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Chất lượng Giỏi/Khá</p>
                                            <h4 class="card-title">
                                                @php
                                                    $quality = 0;
                                                    if ($summary['total_test_quanso'] > 0) {
                                                        $quality = round((($summary['total_good'] + $summary['total_fair']) / $summary['total_test_quanso']) * 100, 2);
                                                    }
                                                @endphp
                                                {{ $quality }}%
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-head-bg-primary">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Đơn vị</th>
                                <th>Nội dung</th>
                                <th>QS tham gia</th>
                                <th>Thời gian (h)</th>
                                <th>Kết quả HL</th>
                                <th>Xếp loại</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->training_date->format('d/m') }}</td>
                                    <td>{{ $log->unit_name_at_time ?? ($log->unit ? $log->unit->name : 'N/A') }}</td>
                                    <td>{{ Str::limit($log->training_content, 40) }}</td>
                                    <td>{{ $log->actual_quanso }}/{{ $log->required_quanso }}</td>
                                    <td>{{ $log->actual_hours }}/{{ $log->required_hours }}</td>
                                    <td>G:{{ $log->good_count }}, K:{{ $log->fair_count }}, TB:{{ $log->pass_count }}, Y:{{ $log->fail_count }}</td>
                                    <td><span class="badge {{ $log->rating_badge }}">{{ $log->rating_name }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Không có dữ liệu trong khoảng thời gian này</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($logs->isNotEmpty())
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="3">TỔNG CỘNG</td>
                                <td>{{ $summary['total_actual_quanso'] }}/{{ $summary['total_required_quanso'] }}</td>
                                <td>{{ $summary['total_actual_hours'] }}/{{ $summary['total_required_hours'] }}</td>
                                <td>G:{{ $summary['total_good'] }}, K:{{ $summary['total_fair'] }}, TB:{{ $summary['total_pass'] }}, Y:{{ $summary['total_fail'] }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
