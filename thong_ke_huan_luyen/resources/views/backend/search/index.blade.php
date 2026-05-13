@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Tìm kiếm hệ thống</h3>
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
                <a href="#">Tìm kiếm</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tra cứu thông tin quân nhân, vũ khí, trang bị</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('search.index') }}" method="GET" class="row g-3 mb-4">
                        <div class="col-md-6">
                            <input type="text" name="q" class="form-control"
                                placeholder="Nhập tên, mã quân nhân, chức vụ..." value="{{ request('q') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="unit_id" class="form-select">
                                <option value="">-- Tất cả đơn vị --</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ $unit->level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>

                    @if (request()->has('q') || request()->has('unit_id'))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã QN</th>
                                        <th>Họ tên</th>
                                        <th>Cấp bậc</th>
                                        <th>Chức vụ</th>
                                        <th>Đơn vị</th>
                                        <th>Trang bị</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($soldiers as $soldier)
                                        <tr>
                                            <td>{{ $soldier->code }}</td>
                                            <td>{{ $soldier->full_name }}</td>
                                            <td>{{ $soldier->rank }}</td>
                                            <td>{{ $soldier->position }}</td>
                                            <td>{{ $soldier->unit ? $soldier->unit->getFullHierarchyName() : 'N/A' }}</td>
                                            <td>
                                                @foreach ($soldier->weapons as $weapon)
                                                    @php
                                                        $details = [];
                                                        if ($weapon->ak) {
                                                            $details[] = "AK: $weapon->ak";
                                                        }
                                                        if ($weapon->rpd) {
                                                            $details[] = "RPD: $weapon->rpd";
                                                        }
                                                        if ($weapon->b41) {
                                                            $details[] = "B41: $weapon->b41";
                                                        }
                                                        if ($weapon->m79) {
                                                            $details[] = "M79: $weapon->m79";
                                                        }
                                                        // if ($weapon->infantry_shovel) {
                                                        //     $details[] = "Xẻng: $weapon->infantry_shovel";
                                                        // }
                                                        // if ($weapon->infantry_pickaxe) {
                                                        //     $details[] = "Cuốc: $weapon->infantry_pickaxe";
                                                        // }
                                                        // if ($weapon->grenade) {
                                                        //     $details[] = "Lựu đạn: $weapon->grenade";
                                                        // }
                                                    @endphp
                                                    @foreach ($details as $detail)
                                                        <span class="badge badge-info mb-1">{{ $detail }}</span>
                                                    @endforeach
                                                    @if (count($details) == 0 && $soldier->weapons->count() > 0)
                                                        <span class="badge badge-secondary">Chưa có trang bị</span>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('soldiers.show', $soldier->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Không tìm thấy kết quả phù hợp</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $soldiers->appends(request()->all())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-4x text-muted mb-3"></i>
                            <p class="text-muted">Nhập từ khóa để bắt đầu tìm kiếm</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
