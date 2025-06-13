@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết học kỳ') }}</span>
                    <div>
                        <a href="{{ route('semesters.index') }}" class="btn btn-secondary btn-sm me-1">
                            <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                        </a>
                        <a href="{{ route('semesters.edit', $semester->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Chỉnh sửa') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin học kỳ') }}</h5>
                        <div class="row">
                            <div class="col-md-4 fw-bold">{{ __('Tên học kỳ:') }}</div>
                            <div class="col-md-8">{{ $semester->name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Năm học:') }}</div>
                            <div class="col-md-8">{{ $semester->academicYear->name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
