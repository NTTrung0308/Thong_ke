@extends('backend.layouts.dashboard')

@section('dashboard_content')
<div class="row mt-4">
    <div class="col-md-12">
        <h2 class="fw-bold mb-4 text-center">TRUNG TÂM CHỈ HUY & ĐIỀU HÀNH</h2>
    </div>
</div>

<!-- 1. Thẻ thống kê tổng quát -->
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round card-primary" style="background: var(--primary-gradient); color: white;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-white">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category text-white opacity-75">Quân số</p>
                            <h4 class="card-title text-white">{{ number_format($totalSoldiers) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round card-info" style="background: var(--info-gradient); color: white;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-white">
                            <i class="fas fa-crosshairs"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category text-white opacity-75">Vũ khí & TB</p>
                            <h4 class="card-title text-white">{{ number_format($totalWeapons) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round card-success" style="background: var(--success-gradient); color: white;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-white">
                            <i class="fas fa-medal"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category text-white opacity-75">Khen thưởng</p>
                            <h4 class="card-title text-white">{{ number_format($totalRewards) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round card-danger" style="background: var(--danger-gradient); color: white;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-white">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category text-white opacity-75">Kỷ luật</p>
                            <h4 class="card-title text-white">{{ number_format($totalDisciplines) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 2. Biểu đồ Quân số -->
    <div class="col-md-8">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Phân bổ quân số theo đơn vị</div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="soldiersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Biểu đồ Kết quả Huấn luyện -->
    <div class="col-md-4">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-title">Kết quả huấn luyện năm {{ date('Y') }}</div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="trainingChart"></canvas>
                </div>
                <div id="chart-legends"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 4. Unit Hierarchy Tree -->
    <div class="col-md-4">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-title">Sơ đồ tổ chức đơn vị</div>
            </div>
            <div class="card-body">
                <div id="unitsTree" style="max-height: 400px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>

    <!-- 5. Activity Stream (Nhật ký hoạt động) -->
    <div class="col-md-8">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Dòng hoạt động gần đây</div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($activities as $activity)
                        <div class="list-group-item d-flex align-items-center p-3">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-title rounded-circle bg-light text-primary border">
                                    <i class="fas {{ $activity->event == 'created' ? 'fa-plus' : ($activity->event == 'updated' ? 'fa-edit' : 'fa-trash') }}"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small">
                                    <span class="fw-bold text-primary">{{ $activity->causer->name ?? 'Hệ thống' }}</span>
                                    <span>{{ $activity->description }}</span>
                                    @if($activity->subject)
                                        <span class="badge badge-outline-secondary small ms-1 text-dark">{{ str_replace('App\\Models\\', '', $activity->subject_type) }}</span>
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    <i class="far fa-clock me-1"></i>{{ $activity->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">Chưa có hoạt động nào được ghi nhận.</div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-link">Xem tất cả nhật ký</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Thêm thư viện TreeView -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>

<script>
    $(document).ready(function() {
        // Biểu đồ quân số (Bar Chart)
        var ctx = document.getElementById('soldiersChart').getContext('2d');
        var soldiersChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartUnitNames) !!},
                datasets: [{
                    label: 'Số lượng quân nhân',
                    data: {!! json_encode($chartUnitCounts) !!},
                    backgroundColor: 'rgba(23, 125, 255, 0.7)',
                    borderColor: 'rgb(23, 125, 255)',
                    borderWidth: 1
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

        // Biểu đồ kết quả huấn luyện (Doughnut Chart)
        var ctx2 = document.getElementById('trainingChart').getContext('2d');
        var trainingChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($resultLabels) !!},
                datasets: [{
                    data: {!! json_encode($resultData) !!},
                    backgroundColor: [
                        '#1d7af3', // Xuất sắc
                        '#2bb930', // Giỏi
                        '#ffad46', // Khá
                        '#f3545d', // Trung bình
                        '#9c27b0'  // Yếu
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                }
            }
        });

        // Load Unit Tree
        $.ajax({
            url: "{{ route('api.units-tree') }}",
            method: 'GET',
            success: function(data) {
                $('#unitsTree').treeview({
                    data: data,
                    enableLinks: false,
                    showTags: true,
                    levels: 2
                });
            }
        });
    });
</script>

<style>
    .card-stats:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
</style>
@endsection
