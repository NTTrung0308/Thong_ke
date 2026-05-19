<div class="row">
    <div class="col-md-4 text-center mb-3">
        <div class="avatar avatar-xxl mb-3">
            <span class="avatar-title rounded-circle border border-white bg-primary text-white" style="font-size: 3rem;">
                {{ substr($soldier->full_name, strrpos($soldier->full_name, ' ') + 1, 1) }}
            </span>
        </div>
        <h4 class="fw-bold mb-1">{{ $soldier->full_name }}</h4>
        <p class="text-muted mb-1">{{ $soldier->code }}</p>
        <span class="badge badge-primary">{{ $soldier->rank }}</span>
    </div>
    <div class="col-md-8">
        <table class="table table-sm table-bordered">
            <tr>
                <th width="35%" class="bg-light">Đơn vị</th>
                <td>{{ $soldier->unit ? $soldier->unit->getFullHierarchyName() : 'N/A' }}</td>
            </tr>
            <tr>
                <th class="bg-light">Chức vụ</th>
                <td>{{ $soldier->position }}</td>
            </tr>
            <tr>
                <th class="bg-light">Ngày nhập ngũ</th>
                <td>{{ $soldier->enlistment_date ? $soldier->enlistment_date->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th class="bg-light">Trình độ chuyên môn</th>
                <td>{{ $soldier->professional_level }}</td>
            </tr>
            <tr>
                <th class="bg-light">Vũ khí biên chế</th>
                <td>
                    @forelse($soldier->weapons as $weapon)
                        @if ($weapon->ak)
                            <span class="badge badge-info">AK: {{ $weapon->ak ?? 'N/A' }}</span>
                        @endif
                        @if ($weapon->rpd)
                            <span class="badge badge-info">RPD: {{ $weapon->rpd ?? 'N/A' }}</span>
                        @endif
                        @if ($weapon->b41)
                            <span class="badge badge-info">B41: {{ $weapon->b41 ?? 'N/A' }}</span>
                        @endif
                        @if ($weapon->m79)
                            <span class="badge badge-info">M79: {{ $weapon->m79 ?? 'N/A' }}</span>
                        @endif
                    @empty
                        <span class="text-muted">Chưa có</span>
                    @endforelse
                </td>
            </tr>
        </table>

        <div class="mt-3">
            <h5 class="fw-bold"><i class="fas fa-medal text-warning me-1"></i> Khen thưởng gần nhất</h5>
            @php $lastReward = $soldier->rewards->sortByDesc('created_at')->first(); @endphp
            <p class="mb-2 small">
                {{ $lastReward ? $lastReward->reason : 'Chưa có dữ liệu khen thưởng' }}
            </p>

            <h5 class="fw-bold"><i class="fas fa-exclamation-triangle text-danger me-1"></i> Kỷ luật gần nhất</h5>
            @php $lastDiscipline = $soldier->disciplines->sortByDesc('created_at')->first(); @endphp
            <p class="mb-0 small">
                {{ $lastDiscipline ? $lastDiscipline->reason : 'Chưa có dữ liệu kỷ luật' }}
            </p>
        </div>
    </div>
</div>
<div class="modal-footer px-0 pb-0 mt-3">
    <a href="{{ route('soldiers.show', $soldier->id) }}" class="btn btn-primary btn-sm">Xem hồ sơ đầy đủ</a>
</div>
