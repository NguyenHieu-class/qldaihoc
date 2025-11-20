@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chỉnh sửa điểm') }}</span>
                    <a href="{{ route('grades.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                    </a>
                </div>

                <div class="card-body">
                    @include('partials.alerts')
                    @include('partials.instructions', ['guideline' => 'Chỉnh sửa thông tin và nhấn Cập nhật để lưu thay đổi.'])

                    @php($isLocked = $isLocked ?? false)

                    @if ($isLocked)
                        <div class="alert alert-info">
                            <i class="fas fa-lock me-2"></i>
                            {{ __('Lớp học phần liên quan đã đóng hoặc đang bị khóa điểm, bạn chỉ có thể xem lại thông tin điểm số.') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('grades.update', $grade->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="student_id" class="form-label">{{ __('Sinh viên') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required {{ $isLocked ? 'disabled' : '' }}>
                                    <option value="">{{ __('-- Chọn sinh viên --') }}</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ (old('student_id', $grade->student_id) == $student->id) ? 'selected' : '' }}>
                                            {{ $student->student_id }} - {{ $student->first_name }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="subject_id" class="form-label">{{ __('Môn học') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required {{ $isLocked ? 'disabled' : '' }}>
                                    <option value="">{{ __('-- Chọn môn học --') }}</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ (old('subject_id', $grade->subject_id) == $subject->id) ? 'selected' : '' }}>
                                            {{ $subject->code }} - {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="midterm_score" class="form-label">{{ __('Điểm giữa kỳ') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.1" min="0" max="10" class="form-control @error('midterm_score') is-invalid @enderror" id="midterm_score" name="midterm_score" value="{{ old('midterm_score', $grade->midterm_score) }}" required {{ $isLocked ? 'disabled' : '' }}>
                                @error('midterm_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="final_score" class="form-label">{{ __('Điểm cuối kỳ') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.1" min="0" max="10" class="form-control @error('final_score') is-invalid @enderror" id="final_score" name="final_score" value="{{ old('final_score', $grade->final_score) }}" required {{ $isLocked ? 'disabled' : '' }}>
                                @error('final_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="assignment_score" class="form-label">{{ __('Điểm bài tập') }}</label>
                                <input type="number" step="0.1" min="0" max="10" class="form-control @error('assignment_score') is-invalid @enderror" id="assignment_score" name="assignment_score" value="{{ old('assignment_score', $grade->assignment_score) }}" {{ $isLocked ? 'disabled' : '' }}>
                                @error('assignment_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="semester_id" class="form-label">{{ __('Học kỳ') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('semester_id') is-invalid @enderror" id="semester_id" name="semester_id" required {{ $isLocked ? 'disabled' : '' }}>
                                    <option value="">{{ __('-- Chọn học kỳ --') }}</option>
                                    @foreach($semesters as $semester)
                                        <option value="{{ $semester->id }}" {{ old('semester_id', $grade->semester_id) == $semester->id ? 'selected' : '' }}>
                                            {{ $semester->name }} ({{ $semester->academicYear->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('semester_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">{{ __('Ghi chú') }}</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3" {{ $isLocked ? 'disabled' : '' }}>{{ old('note', $grade->note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" {{ $isLocked ? 'disabled' : '' }}>
                                <i class="fas fa-save"></i> {{ __('Cập nhật') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 