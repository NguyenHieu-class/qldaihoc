@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ request('auto') ? __('Tạo lớp học phần tự động') : __('Thêm lớp học phần') }}</span>
                    <a href="{{ route('class-sections.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')

                    <div class="mb-3">
                        <form action="{{ route('class-sections.create') }}" method="GET" class="row g-3">
                            <input type="hidden" name="auto" value="{{ request('auto') }}">
                            <div class="col-md-4">
                                <select name="academic_year_id" class="form-select">
                                    <option value="">{{ __('-- Năm học --') }}</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="semester_id" class="form-select">
                                    <option value="">{{ __('-- Học kỳ --') }}</option>
                                    @foreach($semesters as $s)
                                        <option value="{{ $s->id }}" {{ request('semester_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->academicYear->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="faculty_id" class="form-select">
                                    <option value="">{{ __('-- Khoa --') }}</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    @if(request('auto'))
                        <form method="POST" action="{{ route('class-sections.generate') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ __('Mở môn học') }} <span class="text-danger">*</span></label>
                                <select class="form-select" name="course_offering_id" required>
                                    <option value="">{{ __('-- Chọn môn học --') }}</option>
                                    @foreach($courseOfferings as $offering)
                                        <option value="{{ $offering->id }}">{{ $offering->subject->code }} - {{ $offering->subject->name }} ({{ $offering->semester->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Giáo viên') }} <span class="text-danger">*</span></label>
                                <select class="form-select" name="teacher_id" required>
                                    <option value="">{{ __('-- Chọn giáo viên --') }}</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->full_name }} - {{ $teacher->faculty->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Định mức lương') }} <span class="text-danger">*</span></label>
                                <select class="form-select" name="teaching_rate_id" required>
                                    <option value="">{{ __('-- Chọn định mức --') }}</option>
                                    @foreach($teachingRates as $rate)
                                        <option value="{{ $rate->id }}">{{ number_format($rate->amount, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Phòng') }}</label>
                                    <input type="text" class="form-control" name="room" value="{{ old('room') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Số tiết') }}</label>
                                    <input type="number" class="form-control" name="period_count" value="{{ old('period_count', 0) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Số SV') }}</label>
                                    <input type="number" class="form-control" name="student_count" value="{{ old('student_count', 0) }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Số lớp cần tạo') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="number_of_sections" value="{{ old('number_of_sections', 1) }}" min="1">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-magic"></i> {{ __('Tạo tự động') }}
                                </button>
                            </div>
                        </form>
                    @else
                        <form method="POST" action="{{ route('class-sections.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ __('Mã lớp') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="code" value="{{ old('code') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Mở môn học') }} <span class="text-danger">*</span></label>
                                <select class="form-select" name="course_offering_id" required>
                                    <option value="">{{ __('-- Chọn môn học --') }}</option>
                                    @foreach($courseOfferings as $offering)
                                        <option value="{{ $offering->id }}" {{ old('course_offering_id') == $offering->id ? 'selected' : '' }}>{{ $offering->subject->code }} - {{ $offering->subject->name }} ({{ $offering->semester->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Giáo viên') }} <span class="text-danger">*</span></label>
                                <select class="form-select" name="teacher_id" required>
                                    <option value="">{{ __('-- Chọn giáo viên --') }}</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }} - {{ $teacher->faculty->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Định mức lương') }} <span class="text-danger">*</span></label>
                                <select class="form-select" name="teaching_rate_id" required>
                                    <option value="">{{ __('-- Chọn định mức --') }}</option>
                                    @foreach($teachingRates as $rate)
                                        <option value="{{ $rate->id }}" {{ old('teaching_rate_id') == $rate->id ? 'selected' : '' }}>{{ number_format($rate->amount, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Phòng') }}</label>
                                    <input type="text" class="form-control" name="room" value="{{ old('room') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Số tiết') }}</label>
                                    <input type="number" class="form-control" name="period_count" value="{{ old('period_count', 0) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Số SV') }}</label>
                                    <input type="number" class="form-control" name="student_count" value="{{ old('student_count', 0) }}">
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('Lưu') }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
