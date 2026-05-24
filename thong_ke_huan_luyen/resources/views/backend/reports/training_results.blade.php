@extends('backend.layouts.dashboard')

@section('dashboard_content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Báo cáo Kết quả tập huấn</h3>
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
            <a href="{{ route('training-results.index') }}">Kết quả tập huấn</a>
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
                <form action="{{ route('training-results.report') }}" method="GET" class="row">
                    <div class="col-md-4">
                        <div class="form-group p-0">
                            <label>Đơn vị</label>
                            <select name="unit_id" class="form-control">
                                <option value="">Tất cả đơn vị</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->getFullHierarchyName() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                                            <p class="card-category">Tổng số đợt tập huấn</p>
                                            <h4 class="card-title">{{ array_sum(array_column($statsByMonth, 'total')) }}</h4>
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
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Xếp loại Giỏi/Xuất sắc</p>
                                            <h4 class="card-title">{{ $statsByResult['xuất_sắc'] + $statsByResult['giỏi'] }}</h4>
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
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Tổng số giờ huấn luyện</p>
                                            <h4 class="card-title">{{ array_sum(array_column($statsByMonth, 'total_hours')) }}h</h4>
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
                                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                                            <i class="fas fa-percent"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Tỉ lệ đạt yêu cầu TB</p>
                                            <h4 class="card-title">
                                                @php
                                                    $totalCount = array_sum(array_column($statsByMonth, 'total'));
                                                    $avgPassingRate = $totalCount > 0 ? array_sum(array_column($statsByMonth, 'avg_passing_rate')) / $totalCount : 0;
                                                @endphp
                                                {{ round($avgPassingRate, 2) }}%
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Thống kê theo kết quả xếp loại</div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="resultChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Thống kê theo đơn vị</div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Đơn vị</th>
                                                <th>Số đợt</th>
                                                <th>G/XS</th>
                                                <th>Tỉ lệ đạt (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($statsByUnit as $unitName => $stat)
                                                <tr>
                                                    <td>{{ $unitName }}</td>
                                                    <td>{{ $stat['total'] }}</td>
                                                    <td>{{ $stat['excellent'] + $stat['good'] }}</td>
                                                    <td>{{ round($stat['passing_rate_avg'], 2) }}%</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Phân tích huấn luyện</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height:400px">
                            <canvas id="analysisChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Tiến độ huấn luyện theo tháng</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Biểu đồ xếp loại
        var ctxResult = document.getElementById('resultChart').getContext('2d');
        var resultChart = new Chart(ctxResult, {
            type: 'pie',
            data: {
                labels: ['Xuất sắc', 'Giỏi', 'Khá', 'Trung bình', 'Yếu'],
                datasets: [{
                    data: [
                        {{ $statsByResult['xuất_sắc'] }},
                        {{ $statsByResult['giỏi'] }},
                        {{ $statsByResult['khá'] }},
                        {{ $statsByResult['trung_bình'] }},
                        {{ $statsByResult['yếu'] }}
                    ],
                    backgroundColor: ['#28a745', '#007bff', '#17a2b8', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Biểu đồ tháng
        var ctxMonth = document.getElementById('monthChart').getContext('2d');
        var monthChart = new Chart(ctxMonth, {
            type: 'bar',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Số đợt huấn luyện',
                    data: [
                        @foreach($statsByMonth as $stat)
                            {{ $stat['total'] }},
                        @endforeach
                    ],
                    backgroundColor: '#177dff'
                }, {
                    label: 'Số giờ (h)',
                    data: [
                        @foreach($statsByMonth as $stat)
                            {{ $stat['total_hours'] }},
                        @endforeach
                    ],
                    backgroundColor: '#f3545d'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Biểu đồ phân tích (stacked counts by result per month + avg passing rate line)
        var ctxAnalysis = document.getElementById('analysisChart').getContext('2d');
        var analysisChart = new Chart(ctxAnalysis, {
            data: {
                labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
                datasets: [
                    {
                        label: 'Xuất sắc',
                        data: [
                            @for($m=1;$m<=12;$m++) {{ $statsByMonth[$m]['by_result']['xuất_sắc'] }}, @endfor
                        ],
                        backgroundColor: '#28a745',
                        stack: 'Stack 0'
                    },
                    {
                        label: 'Giỏi',
                        data: [
                            @for($m=1;$m<=12;$m++) {{ $statsByMonth[$m]['by_result']['giỏi'] }}, @endfor
                        ],
                        backgroundColor: '#007bff',
                        stack: 'Stack 0'
                    },
                    {
                        label: 'Khá',
                        data: [
                            @for($m=1;$m<=12;$m++) {{ $statsByMonth[$m]['by_result']['khá'] }}, @endfor
                        ],
                        backgroundColor: '#17a2b8',
                        stack: 'Stack 0'
                    },
                    {
                        label: 'Trung bình',
                        data: [
                            @for($m=1;$m<=12;$m++) {{ $statsByMonth[$m]['by_result']['trung_bình'] }}, @endfor
                        ],
                        backgroundColor: '#ffc107',
                        stack: 'Stack 0'
                    },
                    {
                        label: 'Yếu',
                        data: [
                            @for($m=1;$m<=12;$m++) {{ $statsByMonth[$m]['by_result']['yếu'] }}, @endfor
                        ],
                        backgroundColor: '#dc3545',
                        stack: 'Stack 0'
                    },
                    {
                        label: 'Tỉ lệ đạt trung bình (%)',
                        data: [
                            @for($m=1;$m<=12;$m++) {{ round($statsByMonth[$m]['avg_passing_rate'],2) }}, @endfor
                        ],
                        type: 'line',
                        yAxisID: 'y1',
                        borderColor: '#343a40',
                        backgroundColor: '#343a40',
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        position: 'left'
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    });
</script>
@endsection
