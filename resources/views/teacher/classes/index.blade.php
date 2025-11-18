@extends('layouts.app')

@section('title', 'Lớp giảng dạy của tôi - Hệ thống quản lý sinh viên')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ __('Quản lý lớp giảng dạy của tôi') }}</h5>
                        <small class="text-muted">{{ __('Giáo viên:') }} {{ $teacher->full_name }} ({{ $teacher->teacher_id }})</small>
                    </div>
                    <a href="{{ route('dashboard.teacher') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại dashboard') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')

                    <form method="GET" action="{{ route('teacher.classes.index') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="search" class="form-label">{{ __('Tìm kiếm lớp học') }}</label>
                                <input type="text" name="search" id="search" class="form-control"
                                    placeholder="{{ __('Nhập mã lớp hoặc tên môn học...') }}" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="academic_year_id" class="form-label">{{ __('Năm học') }}</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select">
                                    <option value="">{{ __('Tất cả năm học') }}</option>
                                    @foreach ($academicYears as $academicYear)
                                        <option value="{{ $academicYear->id }}"
                                            @selected(request('academic_year_id') == $academicYear->id)>
                                            {{ $academicYear->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="semester_id" class="form-label">{{ __('Học kỳ') }}</label>
                                <select name="semester_id" id="semester_id" class="form-select">
                                    <option value="">{{ __('Tất cả học kỳ') }}</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}"
                                            @selected(request('semester_id') == $semester->id)>
                                            {{ $semester->name }} - {{ optional($semester->academicYear)->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">{{ __('Trạng thái lớp') }}</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">{{ __('Tất cả trạng thái') }}</option>
                                    @foreach ($statuses as $key => $label)
                                        <option value="{{ $key }}" @selected(request('status') == $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> {{ __('Lọc kết quả') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="5%">#</th>
                                    <th scope="col">{{ __('Mã lớp') }}</th>
                                    <th scope="col">{{ __('Môn học') }}</th>
                                    <th scope="col">{{ __('Năm học') }}</th>
                                    <th scope="col">{{ __('Học kỳ') }}</th>
                                    <th scope="col">{{ __('Số tiết') }}</th>
                                    <th scope="col">{{ __('Số sinh viên') }}</th>
                                    <th scope="col">{{ __('Phòng học') }}</th>
                                    <th scope="col">{{ __('Trạng thái') }}</th>
                                    <th scope="col" class="text-center">{{ __('Nhập điểm') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sections as $index => $section)
                                    @php
                                        $courseOffering = $section->courseOffering;
                                        $semester = optional($courseOffering)->semester;
                                        $academicYear = optional($semester)->academicYear;
                                    @endphp
                                    <tr>
                                        <td>{{ $sections->firstItem() + $index }}</td>
                                        <td>
                                            <a href="{{ route('class-sections.show', $section->id) }}" class="text-decoration-none">
                                                {{ $section->code }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $section->subject->code }} - {{ $section->subject->name }}
                                        </td>
                                        <td>{{ $academicYear->name ?? __('Chưa cập nhật') }}</td>
                                        <td>{{ $semester->name ?? __('Chưa cập nhật') }}</td>
                                        <td>{{ $section->period_count }}</td>
                                        <td>{{ $section->student_count }}</td>
                                        <td>{{ $section->room ?? __('Chưa sắp xếp') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $section->status === 'active' ? 'success' : ($section->status === 'closed' ? 'secondary' : 'info') }}">
                                                {{ $section->status_label }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('teacher.classes.gradebook', $section) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-pen"></i> {{ __('Nhập điểm') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle me-2"></i>{{ __('Bạn chưa được phân công lớp học nào.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            {{ __('Tổng số lớp:') }} {{ $sections->total() }}
                        </div>
                        {{ $sections->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
