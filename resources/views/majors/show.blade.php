@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết ngành học') }}</span>
                    <div>
                        <a href="{{ route('majors.index') }}" class="btn btn-secondary btn-sm me-1">
                            <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                        </a>
                        @if(auth()->user()->role == 'admin')
                        <a href="{{ route('majors.edit', $major->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Chỉnh sửa') }}
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin ngành học') }}</h5>
                        <div class="row">
                            <div class="col-md-4 fw-bold">{{ __('Tên ngành học:') }}</div>
                            <div class="col-md-8">{{ $major->name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Mã ngành học:') }}</div>
                            <div class="col-md-8">{{ $major->code }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Khoa:') }}</div>
                            <div class="col-md-8">
                                <a href="{{ route('faculties.show', $major->faculty->id) }}">
                                    {{ $major->faculty->name }}
                                </a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Mô tả:') }}</div>
                            <div class="col-md-8">{{ $major->description ?: __('Không có mô tả') }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Danh sách lớp học') }}</h5>
                        @if($major->classes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">{{ __('STT') }}</th>
                                            <th width="20%">{{ __('Mã lớp') }}</th>
                                            <th width="50%">{{ __('Tên lớp') }}</th>
                                            <th width="25%">{{ __('Năm học') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($major->classes as $key => $class)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $class->code }}</td>
                                            <td>{{ $class->name }}</td>
                                            <td>{{ $class->year }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center">{{ __('Không có lớp học nào thuộc ngành này') }}</p>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h5 class="border-bottom pb-2">{{ __('Thống kê') }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ __('Tổng số lớp học') }}</h5>
                                        <p class="card-text fs-2">{{ $major->classes->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ __('Tổng số sinh viên') }}</h5>
                                        <p class="card-text fs-2">{{ $major->classes->sum(function($class) { return $class->students->count(); }) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 