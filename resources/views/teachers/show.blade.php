@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết giáo viên') }}</span>
                    <div>
                        <a href="{{ route('teachers.index') }}" class="btn btn-secondary btn-sm me-1">
                            <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                        </a>
                        @if(auth()->user()->role == 'admin')
                        <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Chỉnh sửa') }}
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-circle fa-6x text-primary"></i>
                            </div>
                            <h5>{{ $teacher->first_name }} {{ $teacher->last_name }}</h5>
                            <p class="text-muted">{{ $teacher->teacher_id }}</p>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold">{{ __('Khoa:') }}</h6>
                                    <p>{{ $teacher->faculty->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold">{{ __('Email:') }}</h6>
                                    <p>{{ $teacher->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold">{{ __('Số điện thoại:') }}</h6>
                                    <p>{{ $teacher->phone ?? __('Chưa cập nhật') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold">{{ __('Giới tính:') }}</h6>
                                    <p>{{ $teacher->gender }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold">{{ __('Ngày sinh:') }}</h6>
                                    <p>{{ $teacher->date_of_birth->format('d/m/Y') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold">{{ __('Tài khoản:') }}</h6>
                                    <p>{{ $teacher->user_id ? __('Đã kích hoạt') : __('Chưa kích hoạt') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold">{{ __('Địa chỉ:') }}</h6>
                        <p>{{ $teacher->address ?? __('Chưa cập nhật') }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h5 class="mb-3">{{ __('Thông tin khác') }}</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold">{{ __('Ngày tạo:') }}</h6>
                                <p>{{ $teacher->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold">{{ __('Cập nhật lần cuối:') }}</h6>
                                <p>{{ $teacher->updated_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->role == 'admin')
                    <div class="d-flex justify-content-end mt-4">
                        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa giáo viên này?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> {{ __('Xóa giáo viên') }}
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 