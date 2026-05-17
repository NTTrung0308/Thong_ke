<!-- Dashboard Modal -->
<div class="modal fade" id="dashboardModal" tabindex="-1" aria-labelledby="dashboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-5">
                <div class="row mt-2">
                    <div class="col-md-12 text-center">
                        <h2 class="fw-bold mb-4">HỆ THỐNG THỐNG KÊ HUẤN LUYỆN CHIẾN ĐẤU</h2>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <!-- Nút Thống kê huấn luyện -->
                    <div class="col-md-10">
                        <div class="card card-stats card-round p-3 shadow-sm mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Chức năng chính</p>
                                            <h4 class="card-title">Thống kê huấn luyện</h4>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#unitLevelsModal" aria-expanded="false">
                                            <i class="fas fa-chevron-down"></i> Hiển thị thao tác
                                        </button>
                                    </div>
                                </div>

                                <div class="collapse mt-4" id="unitLevelsModal">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        @php
                                            $levels = [
                                                'chi-huy' => 'Cấp Chỉ huy',
                                                'trung-doan' => 'Cấp Trung đoàn',
                                                'tieu-doan' => 'Cấp Tiểu đoàn',
                                                'dai-doi' => 'Cấp Đại đội',
                                                'trung-doi' => 'Cấp Trung đội'
                                            ];

                                            // Xác định cấp cao nhất mà người dùng có thể xem
                                            $maxLevel = 'trung-doi'; // Mặc định thấp nhất
                                            
                                            if (auth()->user()->hasRole('chi-huy')) {
                                                $maxLevel = 'chi-huy';
                                            } elseif (auth()->user()->hasRole('trung-doan')) {
                                                $maxLevel = 'trung-doan';
                                            } elseif (auth()->user()->hasRole('tieu-doan')) {
                                                $maxLevel = 'tieu-doan';
                                            } elseif (auth()->user()->hasRole('dai-doi')) {
                                                $maxLevel = 'dai-doi';
                                            } elseif (auth()->user()->hasRole('trung-doi')) {
                                                $maxLevel = 'trung-doi';
                                            }
                                            
                                            // Nếu có đơn vị, lấy cấp của đơn vị đó làm giới hạn
                                            if (auth()->user()->unit) {
                                                $maxLevel = auth()->user()->unit->level;
                                            }

                                            $show = false;
                                        @endphp

                                        @foreach($levels as $key => $label)
                                            @if($key == $maxLevel)
                                                @php $show = true; @endphp
                                            @endif

                                            @if($show)
                                                <a href="{{ route('soldiers.index', ['level' => $key]) }}" class="btn btn-outline-primary px-3 py-2">
                                                    <i class="fas fa-layer-group mb-1"></i><br>
                                                    <small>{{ $label }}</small>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nút Kiểm tra -->
                    <div class="col-md-10">
                        <div class="card card-stats card-round p-3 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-info bubble-shadow-small">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Tra cứu nhanh</p>
                                            <h4 class="card-title">Kiểm tra</h4>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ route('search.index') }}" class="btn btn-info btn-sm text-white">
                                            <i class="fas fa-search"></i> Truy cập
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #dashboardModal .card-stats:hover {
        transform: translateY(-3px);
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }
</style>
