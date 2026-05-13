@extends('backend.layouts.dashboard')

@section('dashboard_content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chi tiết kết quả tập huấn</h3>
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
                <a href="{{ route('training-results.index') }}">Tập huấn</a>
            </li>
            <li class="separator">
                <i class="fas fa-chevron-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Chi tiết</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Thông tin chi tiết</h4>
                        <div class="ms-auto">
                            <a href="{{ route('training-results.edit', $trainingResult->id) }}"
                                class="btn btn-primary btn-round">
                                <i class="fa fa-edit"></i> Sửa
                            </a>
                            <a href="{{ route('training-results.index') }}" class="btn btn-secondary btn-round">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">Đơn vị</th>
                                    <td>
                                        {{ $trainingResult->unit->getFullHierarchyName() ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày tập huấn</th>
                                    <td>{{ $trainingResult->formatted_training_date }}</td>
                                </tr>
                                <tr>
                                    <th>Thời gian</th>
                                    <td>{{ $trainingResult->formatted_start_time }} -
                                        {{ $trainingResult->formatted_end_time }} ({{ $trainingResult->duration_hours }}
                                        giờ)</td>
                                </tr>
                                <tr>
                                    <th>Quân số tham gia</th>
                                    <td>
                                        Trung đội: {{ $trainingResult->trung_doi_count }} <br>
                                        Tiểu đội (A): {{ $trainingResult->at_count }} <br>
                                        Khẩu đội (KĐT): {{ $trainingResult->kdt_count }} <br>
                                        <strong>Tổng cộng: {{ $trainingResult->total_participants }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">Kết quả chung</th>
                                    <td>
                                        <span class="badge {{ $trainingResult->result_badge }}">
                                            {{ $trainingResult->result_name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tỷ lệ đạt</th>
                                    <td>{{ $trainingResult->passing_rate }}%</td>
                                </tr>
                                <tr>
                                    <th>Giáo viên</th>
                                    <td>{{ $trainingResult->instructor }}</td>
                                </tr>
                                <tr>
                                    <th>Người giám sát</th>
                                    <td>{{ $trainingResult->supervisor }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-secondary card-annoucement card-round">
                                <div class="card-body text-center">
                                    <div class="annoucement-title">
                                        Nội dung tập huấn
                                    </div>
                                    <div class="annoucement-desc">
                                        {!! html_entity_decode($trainingResult->content, ENT_QUOTES | ENT_HTML5, 'UTF-8') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="fw-bold">Đánh giá & Chi tiết</h5>
                            <div class="accordion accordion-secondary">
                                <div class="card">
                                    <div class="card-header" id="headingOne" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <div class="span-icon">
                                            <div class="flaticon-box-1"></div>
                                        </div>
                                        <div class="span-title">
                                            Đánh giá chung & Chi tiết kết quả
                                        </div>
                                        <div class="span-mode"></div>
                                    </div>

                                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                        data-parent="#accordion">
                                        <div class="card-body">
                                            <h6><strong>Đánh giá chung:</strong></h6>
                                            <p>{{ $trainingResult->evaluation ?: 'Không có thông tin' }}</p>
                                            <hr>
                                            <h6><strong>Chi tiết kết quả:</strong></h6>
                                            <p>{{ $trainingResult->result_details ?: 'Không có thông tin' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header collapsed" id="headingTwo" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <div class="span-icon">
                                            <div class="flaticon-success"></div>
                                        </div>
                                        <div class="span-title">
                                            Ưu điểm & Khuyết điểm
                                        </div>
                                        <div class="span-mode"></div>
                                    </div>
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                        data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6><strong>Ưu điểm:</strong></h6>
                                                    <p>{{ $trainingResult->strengths ?: 'Không có thông tin' }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6><strong>Khuyết điểm:</strong></h6>
                                                    <p>{{ $trainingResult->weaknesses ?: 'Không có thông tin' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header collapsed" id="headingThree" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <div class="span-icon">
                                            <div class="flaticon-error"></div>
                                        </div>
                                        <div class="span-title">
                                            Đề xuất & Tài liệu
                                        </div>
                                        <div class="span-mode"></div>
                                    </div>
                                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                        data-parent="#accordion">
                                        <div class="card-body">
                                            <h6><strong>Đề xuất, kiến nghị:</strong></h6>
                                            <p>{{ $trainingResult->recommendations ?: 'Không có thông tin' }}</p>
                                            <hr>
                                            <h6><strong>Tài liệu đính kèm:</strong></h6>
                                            @if ($trainingResult->attachment)
                                                <a href="{{ Storage::url($trainingResult->attachment) }}"
                                                    class="btn btn-link" target="_blank">
                                                    <i class="fa fa-file-pdf"></i> Xem tài liệu đính kèm
                                                </a>
                                            @else
                                                <p>Không có tài liệu đính kèm</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-muted small">
                        Được tạo bởi: {{ $trainingResult->creator ? $trainingResult->creator->name : 'N/A' }} lúc
                        {{ $trainingResult->created_at->format('d/m/Y H:i') }} <br>
                        Cập nhật cuối bởi: {{ $trainingResult->updater ? $trainingResult->updater->name : 'N/A' }} lúc
                        {{ $trainingResult->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
