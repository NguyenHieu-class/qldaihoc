@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết điểm') }}</span>
                    <div>
                        <a href="{{ route('grades.index') }}" class="btn btn-secondary btn-sm me-1">
                            <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                        </a>
                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'teacher')
                        <a href="{{ route('grades.edit', $grade->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Chỉnh sửa') }}
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin sinh viên') }}</h5>
                        <div class="row">
                            <div class="col-md-4 fw-bold">{{ __('Mã sinh viên:') }}</div>
                            <div class="col-md-8">{{ $grade->student->student_id }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Họ và tên:') }}</div>
                            <div class="col-md-8">{{ $grade->student->first_name }} {{ $grade->student->last_name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Lớp:') }}</div>
                            <div class="col-md-8">{{ $grade->student->class->name }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin môn học') }}</h5>
                        <div class="row">
                            <div class="col-md-4 fw-bold">{{ __('Mã môn học:') }}</div>
                            <div class="col-md-8">{{ $grade->subject->code }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Tên môn học:') }}</div>
                            <div class="col-md-8">{{ $grade->subject->name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Số tín chỉ:') }}</div>
                            <div class="col-md-8">{{ $grade->subject->credits }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin điểm') }}</h5>
                        <div class="row">
                            <div class="col-md-4 fw-bold">{{ __('Điểm giữa kỳ:') }}</div>
                            <div class="col-md-8">{{ $grade->midterm_score }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Điểm cuối kỳ:') }}</div>
                            <div class="col-md-8">{{ $grade->final_score }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Điểm tổng kết:') }}</div>
                            <div class="col-md-8">{{ number_format(($grade->midterm_score * 0.3 + $grade->final_score * 0.7), 1) }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Học kỳ:') }}</div>
                            <div class="col-md-8">{{ __('Học kỳ') }} {{ $grade->semester }}</div>
                        </div>
                        @if($grade->note)
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Ghi chú:') }}</div>
                            <div class="col-md-8">{{ $grade->note }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 