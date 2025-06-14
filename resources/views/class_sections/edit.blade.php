@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chỉnh sửa lớp học phần') }}</span>
                    <a href="{{ route('class-sections.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    <form method="POST" action="{{ route('class-sections.update', $classSection->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">{{ __('Mã lớp') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" value="{{ old('code', $classSection->code) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Mở môn học') }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="course_offering_id" required>
                                @foreach($courseOfferings as $offering)
                                    <option value="{{ $offering->id }}" {{ $classSection->course_offering_id == $offering->id ? 'selected' : '' }}>{{ $offering->subject->code }} - {{ $offering->subject->name }} ({{ $offering->semester->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Giáo viên') }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="teacher_id" required>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ $classSection->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }} - {{ $teacher->faculty->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Phòng') }}</label>
                                <input type="text" class="form-control" name="room" value="{{ old('room', $classSection->room) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Số tiết') }}</label>
                                <input type="number" class="form-control" name="period_count" value="{{ old('period_count', $classSection->period_count) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Số SV') }}</label>
                                <input type="number" class="form-control" name="student_count" value="{{ old('student_count', $classSection->student_count) }}">
                            </div>
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
