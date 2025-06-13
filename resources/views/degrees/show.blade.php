@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết học vị') }}</span>
                    <div>
                        <a href="{{ route('degrees.index') }}" class="btn btn-secondary btn-sm me-1">
                            <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                        </a>
                        @if(auth()->user()->role == 'admin')
                        <a href="{{ route('degrees.edit', $degree->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Chỉnh sửa') }}
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin học vị') }}</h5>
                        <div class="row">
                            <div class="col-md-4 fw-bold">{{ __('Tên học vị:') }}</div>
                            <div class="col-md-8">{{ $degree->name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Hệ số:') }}</div>
                            <div class="col-md-8">{{ $degree->coefficient }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h5 class="border-bottom pb-2">{{ __('Danh sách giáo viên') }}</h5>
                        @if($degree->teachers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">{{ __('STT') }}</th>
                                            <th width="20%">{{ __('Mã giáo viên') }}</th>
                                            <th width="50%">{{ __('Họ tên') }}</th>
                                            <th width="25%">{{ __('Email') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($degree->teachers as $key => $teacher)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $teacher->teacher_id }}</td>
                                            <td>{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                            <td>{{ $teacher->email }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center">{{ __('Không có giáo viên nào có học vị này') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
