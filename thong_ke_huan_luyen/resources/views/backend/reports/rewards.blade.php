@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Báo cáo thống kê khen thưởng</h3>
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
                <a href="{{ route('rewards.index') }}">Khen thưởng</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Báo cáo thống kê</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Bộ lọc thống kê</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('rewards.report') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Chọn năm báo cáo</label>
                                    <select name="year" class="form-control" onchange="this.form.submit()">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                                Năm {{ $year }}
                                            </option>
                                        @endforeach
                                        @if(!in_array(date('Y'), $years->toArray()))
                                            <option value="{{ date('Y') }}" {{ $currentYear == date('Y') ? 'selected' : '' }}>
                                                Năm {{ date('Y') }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Thống kê theo đơn vị năm {{ $currentYear }}</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-head-bg-primary">
                            <thead>
                                <tr>
                                    <th>Đơn vị</th>
                                    <th class="text-center">Khen thưởng Đơn vị</th>
                                    <th class="text-center">Khen thưởng Quân nhân</th>
                                    <th class="text-center">Tổng cộng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalAll = 0; $totalUnit = 0; $totalSuperior = 0; @endphp
                                @foreach($statsByUnit as $unitName => $stat)
                                    <tr>
                                        <td>{{ $unitName }}</td>
                                        <td class="text-center">{{ $stat['unit_rewards'] }}</td>
                                        <td class="text-center">{{ $stat['superior_rewards'] }}</td>
                                        <td class="text-center fw-bold">{{ $stat['total'] }}</td>
                                    </tr>
                                    @php 
                                        $totalAll += $stat['total']; 
                                        $totalUnit += $stat['unit_rewards'];
                                        $totalSuperior += $stat['superior_rewards'];
                                    @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light fw-bold">
                                    <td>Tổng cộng hệ thống</td>
                                    <td class="text-center">{{ $totalUnit }}</td>
                                    <td class="text-center">{{ $totalSuperior }}</td>
                                    <td class="text-center text-primary" style="font-size: 1.1rem">{{ $totalAll }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Cơ cấu cấp quyết định</div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="decisionLevelChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <ul class="list-group list-group-unbordered">
                            @foreach($statsByLevel as $level => $count)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $level }}
                                    <span class="badge badge-primary badge-pill">{{ $count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Thống kê theo hình thức khen thưởng</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <canvas id="rewardFormChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Hình thức</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-center">Tỷ lệ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($statsByForm as $form => $count)
                                            <tr>
                                                <td>{{ $form }}</td>
                                                <td class="text-center">{{ $count }}</td>
                                                <td class="text-center">
                                                    {{ $totalAll > 0 ? round(($count / $totalAll) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('backend/assets/js/plugin/chart.js/chart.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Biểu đồ cơ cấu cấp quyết định
        var ctxLevel = document.getElementById('decisionLevelChart').getContext('2d');
        var decisionLevelChart = new Chart(ctxLevel, {
            type: 'pie',
            data: {
                datasets: [{
                    data: {!! json_encode($statsByLevel->values()) !!},
                    backgroundColor: ['#1d7af3', '#f3545d', '#fdaf4b', '#59d05d', '#177dff', '#716aca', '#212529'],
                    borderWidth: 0
                }],
                labels: {!! json_encode($statsByLevel->keys()) !!}
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom',
                    labels: {
                        fontColor: 'rgb(154, 154, 154)',
                        fontSize: 11,
                        usePointStyle: true,
                        padding: 20
                    }
                },
                pieceLabel: {
                    render: 'percentage',
                    fontColor: 'white',
                    fontSize: 14,
                },
                tooltips: false,
                layout: {
                    padding: {
                        left: 20,
                        right: 20,
                        top: 20,
                        bottom: 20
                    }
                }
            }
        });

        // Biểu đồ hình thức khen thưởng
        var ctxForm = document.getElementById('rewardFormChart').getContext('2d');
        var rewardFormChart = new Chart(ctxForm, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: {!! json_encode($statsByForm->values()) !!},
                    backgroundColor: ['#1d7af3', '#f3545d', '#fdaf4b', '#59d05d', '#177dff', '#716aca', '#212529', '#e83e8c'],
                    borderWidth: 0
                }],
                labels: {!! json_encode($statsByForm->keys()) !!}
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom',
                    labels: {
                        fontColor: 'rgb(154, 154, 154)',
                        fontSize: 11,
                        usePointStyle: true,
                        padding: 20
                    }
                },
                layout: {
                    padding: {
                        left: 20,
                        right: 20,
                        top: 20,
                        bottom: 20
                    }
                }
            }
        });
    });
</script>
@endsection
