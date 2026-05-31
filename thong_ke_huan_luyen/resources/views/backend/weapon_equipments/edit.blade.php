@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chỉnh sửa Biên chế Vũ khí - Trang bị</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a>
            </li>
            <li class="separator"><i class="fas fa-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ route('weapon-equipments.index') }}">Vũ khí trang bị</a>
            </li>
            <li class="separator"><i class="fas fa-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chỉnh sửa</a></li>
        </ul>
    </div>

    <form action="{{ route('weapon-equipments.update', $weaponEquipment->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Thông tin chung -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Thông tin cơ bản (Cập nhật lần cuối bởi: {{ $weaponEquipment->updater->name ?? 'N/A' }})</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Quân nhân nhận <span class="text-danger">*</span></label>
                                    <select class="form-select @error('soldier_id') is-invalid @enderror" name="soldier_id" required>
                                        @foreach($soldiers as $soldier)
                                            <option value="{{ $soldier->id }}" {{ (old('soldier_id', $weaponEquipment->soldier_id) == $soldier->id) ? 'selected' : '' }}>
                                                {{ $soldier->full_name }} ({{ $soldier->unit->name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('soldier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="d-block mb-2">Đơn vị quản lý <span class="text-danger">*</span></label>
                                    <div class="row g-2">
                                        @php
                                            $levelMap = [
                                                'chi-huy' => ['label' => 'Bộ chỉ huy', 'id' => 'unit_chi_huy'],
                                                'trung-doan' => ['label' => 'Trung đoàn', 'id' => 'unit_trung_doan'],
                                                'tieu-doan' => ['label' => 'Tiểu đoàn', 'id' => 'unit_tieu_doan'],
                                                'dai-doi' => ['label' => 'Đại đội', 'id' => 'unit_dai_doi'],
                                                'trung-doi' => ['label' => 'Trung đội', 'id' => 'unit_trung_doi'],
                                            ];
                                            $levelKeys = array_keys($levelMap);
                                        @endphp

                                        @foreach($levelKeys as $index => $levelKey)
                                            <div class="col-md">
                                                <label class="small text-muted">{{ $index + 1 }}. {{ $levelMap[$levelKey]['label'] }}</label>
                                                <select class="form-select form-control unit-selector" data-level="{{ $levelKey }}" id="{{ $levelMap[$levelKey]['id'] }}" {{ !isset($levelOptions[$index]) ? 'disabled' : '' }}>
                                                    <option value="">-- Chọn {{ $levelMap[$levelKey]['label'] }} --</option>
                                                    @if(isset($levelOptions[$index]))
                                                        @foreach($levelOptions[$index] as $option)
                                                            <option value="{{ $option->id }}" {{ (isset($hierarchy[$index]) && $hierarchy[$index]->id == $option->id) ? 'selected' : '' }}>
                                                                {{ $option->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="unit_id" id="final_unit_id" value="{{ old('unit_id', $weaponEquipment->unit_id) }}" required>
                                    @error('unit_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select" name="status" required>
                                        <option value="dang-su-dung" {{ (old('status', $weaponEquipment->status) == 'dang-su-dung') ? 'selected' : '' }}>Đang sử dụng</option>
                                        <option value="da-tra" {{ (old('status', $weaponEquipment->status) == 'da-tra') ? 'selected' : '' }}>Đã trả</option>
                                        <option value="dang-bao-duong" {{ (old('status', $weaponEquipment->status) == 'dang-bao-duong') ? 'selected' : '' }}>Đang bảo dưỡng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tình trạng kỹ thuật <span class="text-danger">*</span></label>
                                    <select class="form-select" name="condition" required>
                                        <option value="tot" {{ (old('condition', $weaponEquipment->condition) == 'tot') ? 'selected' : '' }}>Tốt</option>
                                        <option value="hỏng" {{ (old('condition', $weaponEquipment->condition) == 'hỏng') ? 'selected' : '' }}>Hỏng</option>
                                        <option value="cần_bảo_dưỡng" {{ (old('condition', $weaponEquipment->condition) == 'cần_bảo_dưỡng') ? 'selected' : '' }}>Cần bảo dưỡng</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nhóm Vũ khí súng -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="card-title text-white">Vũ khí (Số hiệu súng)</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Súng AK</label>
                                    <input type="text" class="form-control" name="ak" value="{{ old('ak', $weaponEquipment->ak) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Súng RPD</label>
                                    <input type="text" class="form-control" name="rpd" value="{{ old('rpd', $weaponEquipment->rpd) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Súng B41</label>
                                    <input type="text" class="form-control" name="b41" value="{{ old('b41', $weaponEquipment->b41) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Súng M79</label>
                                    <input type="text" class="form-control" name="m79" value="{{ old('m79', $weaponEquipment->m79) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nhóm Phụ kiện súng -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <div class="card-title text-white">Phụ kiện & Trang bị đi kèm</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Thông nòng</label>
                                    <input type="text" class="form-control" name="cleaning_rod" value="{{ old('cleaning_rod', $weaponEquipment->cleaning_rod) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phụ tùng</label>
                                    <input type="text" class="form-control" name="spare_parts" value="{{ old('spare_parts', $weaponEquipment->spare_parts) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Dây súng</label>
                                    <input type="text" class="form-control" name="gun_strap" value="{{ old('gun_strap', $weaponEquipment->gun_strap) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hộp tiếp đạn (Số lượng)</label>
                                    <input type="number" class="form-control" name="magazine_box" value="{{ old('magazine_box', $weaponEquipment->magazine_box) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Vịt dầu</label>
                                    <input type="number" class="form-control" name="oil_can" value="{{ old('oil_can', $weaponEquipment->oil_can) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ống ngắm (Số hiệu)</label>
                                    <input type="text" class="form-control" name="sight" value="{{ old('sight', $weaponEquipment->sight) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Lựu đạn (Số lượng)</label>
                                    <input type="number" class="form-control" name="grenade" value="{{ old('grenade', $weaponEquipment->grenade) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nhóm Trang bị bộ binh -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <div class="card-title text-white">Trang bị bộ binh</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bao đồ</label>
                                    <input type="text" class="form-control" name="bag" value="{{ old('bag', $weaponEquipment->bag) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Áo súng</label>
                                    <input type="text" class="form-control" name="gun_cover" value="{{ old('gun_cover', $weaponEquipment->gun_cover) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bịt nòng</label>
                                    <input type="text" class="form-control" name="muzzle_cover" value="{{ old('muzzle_cover', $weaponEquipment->muzzle_cover) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Xẻng bộ binh</label>
                                    <input type="number" class="form-control" name="infantry_shovel" value="{{ old('infantry_shovel', $weaponEquipment->infantry_shovel) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cuốc bộ binh</label>
                                    <input type="number" class="form-control" name="infantry_pickaxe" value="{{ old('infantry_pickaxe', $weaponEquipment->infantry_pickaxe) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thời gian & Ghi chú -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <div class="card-title text-white">Thời gian & Ghi chú</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ngày cấp phát</label>
                                    <input type="date" class="form-control" name="receive_date" value="{{ old('receive_date', $weaponEquipment->receive_date ? $weaponEquipment->receive_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Người cấp/Giao</label>
                                    <input type="text" class="form-control" name="received_by" value="{{ old('received_by', $weaponEquipment->received_by) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ngày thu hồi</label>
                                    <input type="date" class="form-control" name="return_date" value="{{ old('return_date', $weaponEquipment->return_date ? $weaponEquipment->return_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Người nhận thu hồi</label>
                                    <input type="text" class="form-control" name="returned_by" value="{{ old('returned_by', $weaponEquipment->returned_by) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Ghi chú thêm (Thay đổi trang bị)</label>
                                    <textarea class="form-control" name="notes" rows="3">{{ old('notes', $weaponEquipment->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-5 text-center">
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-lg">Cập nhật thông tin</button>
                        <a href="{{ route('weapon-equipments.index') }}" class="btn btn-danger btn-lg">Hủy bỏ</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];

    function updateFinalUnitId() {
        let lastId = '';
        levels.forEach(level => {
            const val = $(`#unit_${level.replace('-', '_')}`).val();
            if (val) lastId = val;
        });
        $('#final_unit_id').val(lastId);
    }

    $('.unit-selector').on('change', function() {
        const parentId = $(this).val();
        const currentLevel = $(this).data('level');
        const currentIndex = levels.indexOf(currentLevel);

        // Reset all lower levels
        for (let i = currentIndex + 1; i < levels.length; i++) {
            const $nextSelect = $(`#unit_${levels[i].replace('-', '_')}`);
            $nextSelect.html(`<option value="">-- Chọn ${$nextSelect.prev('label').text().split('. ')[1]} --</option>`);
            $nextSelect.prop('disabled', true);
        }

        updateFinalUnitId();

        if (parentId && currentIndex < levels.length - 1) {
            const nextLevel = levels[currentIndex + 1];
            const $nextSelect = $(`#unit_${nextLevel.replace('-', '_')}`);

            $.ajax({
                url: `{{ route('units.getChildren', '') }}/${parentId}`,
                type: 'GET',
                success: function(data) {
                    if (data.length > 0) {
                        let options = `<option value="">-- Chọn ${$nextSelect.prev('label').text().split('. ')[1]} --</option>`;
                        data.forEach(unit => {
                            options += `<option value="${unit.id}">${unit.name}</option>`;
                        });
                        $nextSelect.html(options);
                        $nextSelect.prop('disabled', false);
                    }
                }
            });
        }
    });
});
</script>
@endsection
