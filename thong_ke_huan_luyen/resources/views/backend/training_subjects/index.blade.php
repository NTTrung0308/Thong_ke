@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Quản lý Nội dung huấn luyện</h3>
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
                <a href="#">Hệ thống</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Nội dung huấn luyện</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Danh mục Nội dung huấn luyện</h4>
                        <a href="{{ route('training-subjects.create') }}" class="btn btn-primary btn-round ms-auto">
                            <i class="fa fa-plus"></i>
                            Thêm nội dung mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="basic-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tên nội dung</th>
                                    <th>Cấp đơn vị</th>
                                    <th>Phân cấp</th>
                                    <th>Người tạo</th>
                                    <th style="width: 10%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subjects as $subject)
                                    <tr>
                                        <td>{{ $subject->name }}</td>
                                        <td>
                                            <span class="badge {{ $subject->unit_level == 'dai-doi' ? 'badge-info' : 'badge-primary' }}">
                                                {{ $subject->unit_level == 'dai-doi' ? 'Đại đội' : 'Trung đội' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($subject->parent)
                                                {{ $subject->parent->name }}
                                            @else
                                                <span class="text-muted">Gốc (Môn học)</span>
                                            @endif
                                        </td>
                                        <td>{{ $subject->creator->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('training-subjects.edit', $subject->id) }}"
                                                    class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip"
                                                    title="Sửa">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('training-subjects.destroy', $subject->id) }}"
                                                    method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link btn-danger"
                                                        data-bs-toggle="tooltip" title="Xóa"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
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
@endsection
