@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chỉnh sửa mở môn học') }}</span>
                    <a href="{{ route('course-offerings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    <form method="POST" action="{{ route('course-offerings.update', $courseOffering->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="subject_id" class="form-label">{{ __('Môn học') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="subject_id" name="subject_id" required>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ $courseOffering->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->code }} - {{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="semester_id" class="form-label">{{ __('Học kỳ') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="semester_id" name="semester_id" required>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ $courseOffering->semester_id == $semester->id ? 'selected' : '' }}>{{ $semester->name }} ({{ $semester->academicYear->name }})</option>
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
