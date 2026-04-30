@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Báo cáo thống kê kỷ luật</h3>
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
                <a href="{{ route('disciplines.index') }}">Kỷ luật</a>
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
                    <form method="GET" action="{{ route('disciplines.report') }}">
                        <div class="row">
                            <div class="col-md-3">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Đơn vị</label>
                                    <select name="unit_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả đơn vị</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
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
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-file-contract"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Tổng số vụ</p>
                                <h4 class="card-title">{{ array_sum($statsByStatus) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Đang thi hành</p>
                                <h4 class="card-title">{{ $statsByStatus['dang-thi-hanh'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
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
                                <p class="card-category">Đã thi hành xong</p>
                                <h4 class="card-title">{{ $statsByStatus['da-thi-hanh-xong'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                <i class="fas fa-user-slash"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Được xóa bỏ</p>
                                <h4 class="card-title">{{ $statsByStatus['duoc-xoa-bo'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Phân tích kỷ luật theo đơn vị năm {{ $currentYear }}</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-head-bg-danger">
                            <thead>
                                <tr>
                                    <th>Đơn vị</th>
                                    <th class="text-center">Cảnh cáo/Khiển trách</th>
                                    <th class="text-center">Cách chức/Giáng cấp</th>
                                    <th class="text-center">Buộc thôi việc</th>
                                    <th class="text-center fw-bold">Tổng số</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statsByUnit as $unitName => $stat)
                                    <tr>
                                        <td>{{ $unitName }}</td>
                                        <td class="text-center">{{ $stat['warning'] + $stat['reprimand'] }}</td>
                                        <td class="text-center">{{ $stat['demotion'] }}</td>
                                        <td class="text-center">{{ $stat['dismissal'] }}</td>
                                        <td class="text-center fw-bold">{{ $stat['total'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Hình thức kỷ luật</div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="disciplineFormChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Thống kê theo cấp quyết định</div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="chart-container">
                                <canvas id="decisionLevelChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Cấp quyết định</th>
                                            <th class="text-center">Số lượng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($statsByLevel as $level => $count)
                                            <tr>
                                                <td>{{ $level }}</td>
                                                <td class="text-center"><span class="badge badge-primary">{{ $count }}</span></td>
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
        // Biểu đồ hình thức kỷ luật
        var ctxForm = document.getElementById('disciplineFormChart').getContext('2d');
        var disciplineFormChart = new Chart(ctxForm, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: {!! json_encode($statsByForm->values()) !!},
                    backgroundColor: ['#f3545d', '#fdaf4b', '#1d7af3', '#59d05d', '#177dff', '#716aca', '#212529'],
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

        // Biểu đồ cấp quyết định
        var ctxLevel = document.getElementById('decisionLevelChart').getContext('2d');
        var decisionLevelChart = new Chart(ctxLevel, {
            type: 'bar',
            data: {
                labels: {!! json_encode($statsByLevel->keys()) !!},
                datasets: [{
                    label: "Số vụ kỷ luật",
                    backgroundColor: '#f3545d',
                    borderColor: '#f3545d',
                    data: {!! json_encode($statsByLevel->values()) !!},
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                },
            }
        });
    });
</script>
@endsection
