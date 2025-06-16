@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Thêm mở môn học') }}</span>
                    <a href="{{ route('course-offerings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')

                    <div class="mb-3">
                        <form action="{{ route('course-offerings.create') }}" method="GET" class="row g-3">
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

                    <form method="POST" action="{{ route('course-offerings.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Môn học') }} <span class="text-danger">*</span></label>
                            <div class="ms-2">
                                @forelse($subjects as $subject)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="subject_ids[]" id="subject_{{ $subject->id }}" value="{{ $subject->id }}" {{ in_array($subject->id, old('subject_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="subject_{{ $subject->id }}">
                                            {{ $subject->code }} - {{ $subject->name }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted">{{ __('Không có môn học') }}</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="semester_id" class="form-label">{{ __('Học kỳ') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="semester_id" name="semester_id" required>
                                <option value="">{{ __('-- Chọn học kỳ --') }}</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ old('semester_id', request('semester_id')) == $semester->id ? 'selected' : '' }}>{{ $semester->name }} ({{ $semester->academicYear->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Lưu') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
