@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Cấp phát Vũ khí - Trang bị</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a>
            </li>
            <li class="separator"><i class="fas fa-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('weapon-equipments.index') }}">Vũ khí trang bị</a></li>
            <li class="separator"><i class="fas fa-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Cấp phát mới</a></li>
        </ul>
    </div>

    <form action="{{ route('weapon-equipments.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Thông tin chung -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Thông tin cơ bản</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Quân nhân nhận <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('soldier_id') is-invalid @enderror" name="soldier_id" required>
                                        <option value="">-- Chọn quân nhân --</option>
                                        @foreach($soldiers as $soldier)
                                            <option value="{{ $soldier->id }}" {{ old('soldier_id') == $soldier->id ? 'selected' : '' }}>
                                                {{ $soldier->full_name }} ({{ $soldier->unit->name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('soldier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Đơn vị quản lý <span class="text-danger">*</span></label>
                                    <select class="form-select @error('unit_id') is-invalid @enderror" name="unit_id" required>
                                        <option value="">-- Chọn đơn vị --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select" name="status" required>
                                        <option value="dang-su-dung" {{ old('status') == 'dang-su-dung' ? 'selected' : '' }}>Đang sử dụng</option>
                                        <option value="da-thu-hoi" {{ old('status') == 'da-thu-hoi' ? 'selected' : '' }}>Đã thu hồi</option>
                                        <option value="bao-quan" {{ old('status') == 'bao-quan' ? 'selected' : '' }}>Đang bảo quản</option>
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
                                    <input type="text" class="form-control" name="ak" value="{{ old('ak') }}" placeholder="Số hiệu súng AK">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Súng RPD</label>
                                    <input type="text" class="form-control" name="rpd" value="{{ old('rpd') }}" placeholder="Số hiệu súng RPD">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Súng B41</label>
                                    <input type="text" class="form-control" name="b41" value="{{ old('b41') }}" placeholder="Số hiệu súng B41">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Súng M79</label>
                                    <input type="text" class="form-control" name="m79" value="{{ old('m79') }}" placeholder="Số hiệu súng M79">
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
                                    <label>Hộp tiếp đạn (Số lượng)</label>
                                    <input type="number" class="form-control" name="magazine_box" value="{{ old('magazine_box') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Thông nòng, phụ kiện khác</label>
                                    <input type="text" class="form-control" name="gun_accessories" value="{{ old('gun_accessories') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ống ngắm (Số hiệu)</label>
                                    <input type="text" class="form-control" name="sight" value="{{ old('sight') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Lựu đạn (Số lượng)</label>
                                    <input type="number" class="form-control" name="grenade" value="{{ old('grenade') }}">
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
                                    <label>Xẻng bộ binh</label>
                                    <input type="number" class="form-control" name="infantry_shovel" value="{{ old('infantry_shovel', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cuốc bộ binh</label>
                                    <input type="number" class="form-control" name="infantry_pickaxe" value="{{ old('infantry_pickaxe', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Túi đựng, bao súng...</label>
                                    <input type="text" class="form-control" name="bag" value="{{ old('bag') }}" placeholder="Túi, bao xe...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ống dầu</label>
                                    <input type="number" class="form-control" name="oil_can" value="{{ old('oil_can', 0) }}">
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
                                    <input type="date" class="form-control" name="receive_date" value="{{ old('receive_date', date('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Người cấp/Giao</label>
                                    <input type="text" class="form-control" name="received_by" value="{{ old('received_by') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Ghi chú thêm</label>
                                    <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-5">
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-success btn-lg">Lưu thông tin cấp phát</button>
                        <a href="{{ route('weapon-equipments.index') }}" class="btn btn-danger btn-lg">Hủy bỏ</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
