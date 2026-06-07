@php
    $requestLevel = request('level');
    $requestUnitId = request('unit_id');
    $currentUnit = null;
    $ancestors = collect([]);
    
    if ($requestUnitId) {
        $currentUnit = \App\Models\Unit::find($requestUnitId);
        if ($currentUnit) {
            $ancestors = $currentUnit->getAncestors();
        }
    }

    $levelLabels = [
        'chi-huy' => 'Cấp Chỉ huy',
        'trung-doan' => 'Cấp Trung đoàn',
        'tieu-doan' => 'Cấp Tiểu đoàn',
        'dai-doi' => 'Cấp Đại đội',
        'trung-doi' => 'Cấp Trung đội'
    ];
@endphp

<ul class="breadcrumbs mb-3 animate__animated animate__fadeInLeft delay-1">
    <li class="nav-home">
        <a href="{{ route('dashboard') }}">
            <i class="fas fa-home"></i>
        </a>
    </li>
    
    @if($requestLevel && isset($levelLabels[$requestLevel]))
        <li class="separator"><i class="fas fa-chevron-right"></i></li>
        <li class="nav-item">
            <a href="{{ route('dashboard') }}">{{ $levelLabels[$requestLevel] }}</a>
        </li>
    @endif

    @foreach($ancestors as $ancestor)
        <li class="separator"><i class="fas fa-chevron-right"></i></li>
        <li class="nav-item">
            <a href="{{ request()->fullUrlWithQuery(['unit_id' => $ancestor->id]) }}">{{ $ancestor->name }}</a>
        </li>
    @endforeach

    @if($currentUnit)
        <li class="separator"><i class="fas fa-chevron-right"></i></li>
        <li class="nav-item">
            <a href="{{ request()->fullUrlWithQuery(['unit_id' => $currentUnit->id]) }}">{{ $currentUnit->name }}</a>
        </li>
    @endif

    <li class="separator"><i class="fas fa-chevron-right"></i></li>
    <li class="nav-item">
        <a href="{{ $activeRoute ?? '#' }}">{{ $activeLabel ?? 'Danh sách' }}</a>
    </li>
</ul>
