@extends('layouts.app')

@section('title', 'Lớp học của tôi - Hệ thống quản lý sinh viên')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ __('Lớp học của tôi') }}</h5>
                        <small class="text-muted">{{ __('Sinh viên:') }} {{ $student->full_name }} ({{ $student->student_id }})</small>
                    </div>
                    <a href="{{ route('dashboard.student') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại dashboard') }}
                    </a>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('Các lớp học phần đang hoạt động mà bạn đã đăng ký sẽ hiển thị tại đây. Khi lớp học phần đóng, lớp sẽ chuyển xuống danh sách các lớp đã học.') }}</p>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <strong>{{ __('Lớp học phần đang hoạt động') }}</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Mã lớp') }}</th>
                                            <th>{{ __('Môn học') }}</th>
                                            <th>{{ __('Giáo viên') }}</th>
                                            <th>{{ __('Học kỳ') }}</th>
                                            <th>{{ __('Trạng thái') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($activeEnrollments as $enrollment)
                                            @php
                                                $classSection = $enrollment->classSection;
                                                $courseOffering = optional($classSection)->courseOffering;
                                                $semester = optional($courseOffering)->semester;
                                                $academicYear = optional($semester)->academicYear;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a href="{{ route('class-sections.show', $classSection->id) }}" class="text-decoration-none">
                                                        {{ $classSection->code }}
                                                    </a>
                                                </td>
                                                <td>{{ $classSection->subject->code }} - {{ $classSection->subject->name }}</td>
                                                <td>{{ $classSection->teacher->full_name }}</td>
                                                <td>{{ $semester->name ?? __('Chưa cập nhật') }} ({{ $academicYear->name ?? __('N/A') }})</td>
                                                <td>
                                                    <span class="badge bg-success">{{ $classSection->status_label }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">{{ __('Chưa có lớp học phần nào đang hoạt động.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <strong>{{ __('Tất cả các lớp đã học') }}</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Mã lớp') }}</th>
                                            <th>{{ __('Môn học') }}</th>
                                            <th>{{ __('Giáo viên') }}</th>
                                            <th>{{ __('Học kỳ') }}</th>
                                            <th>{{ __('Trạng thái') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($completedEnrollments as $enrollment)
                                            @php
                                                $classSection = $enrollment->classSection;
                                                $courseOffering = optional($classSection)->courseOffering;
                                                $semester = optional($courseOffering)->semester;
                                                $academicYear = optional($semester)->academicYear;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a href="{{ route('class-sections.show', $classSection->id) }}" class="text-decoration-none">
                                                        {{ $classSection->code }}
                                                    </a>
                                                </td>
                                                <td>{{ $classSection->subject->code }} - {{ $classSection->subject->name }}</td>
                                                <td>{{ $classSection->teacher->full_name }}</td>
                                                <td>{{ $semester->name ?? __('Chưa cập nhật') }} ({{ $academicYear->name ?? __('N/A') }})</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $classSection->status_label }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">{{ __('Chưa có lớp học phần nào đã hoàn thành.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
