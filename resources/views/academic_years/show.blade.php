@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết năm học') }}</span>
                    <div>
                        <a href="{{ route('academic-years.index') }}" class="btn btn-secondary btn-sm me-1">
                            <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                        </a>
                        <a href="{{ route('academic-years.edit', $academicYear->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Chỉnh sửa') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin năm học') }}</h5>
                        <div class="row">
                            <div class="col-md-4 fw-bold">{{ __('Tên năm học:') }}</div>
                            <div class="col-md-8">{{ $academicYear->name }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h5 class="border-bottom pb-2">{{ __('Danh sách học kỳ') }}</h5>
                        @if($academicYear->semesters->count() > 0)
                            <ul>
                                @foreach($academicYear->semesters as $semester)
                                    <li>{{ $semester->name }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-center">{{ __('Không có học kỳ nào cho năm học này') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
