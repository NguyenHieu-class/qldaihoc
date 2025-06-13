@extends('layouts.app')

@section('title', 'Teacher Dashboard - Hệ thống quản lý sinh viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Dashboard Giáo viên</h2>
    </div>

    <!-- Thông tin giáo viên -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin cá nhân</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 text-center mb-3">
                            <div class="avatar-circle mb-3">
                                <i class="fas fa-user-circle fa-6x text-primary"></i>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Mã giáo viên:</strong></p>
                                    <p>{{ $teacher->teacher_id }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Họ tên:</strong></p>
                                    <p>{{ $teacher->first_name }} {{ $teacher->last_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Email:</strong></p>
                                    <p>{{ $teacher->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Số điện thoại:</strong></p>
                                    <p>{{ $teacher->phone ?? 'Chưa cập nhật' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Khoa:</strong></p>
                                    <p>{{ $faculty->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Ngày sinh:</strong></p>
                                    <p>{{ $teacher->date_of_birth->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Sinh viên trong khoa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $studentCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Lớp học trong khoa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $classCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Ngành học trong khoa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $majorCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Các liên kết nhanh -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Truy cập nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('students.index') }}" class="btn btn-primary btn-block py-3">
                                <i class="fas fa-user-graduate mr-2"></i> Quản lý sinh viên
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('grades.index') }}" class="btn btn-success btn-block py-3">
                                <i class="fas fa-chart-bar mr-2"></i> Quản lý điểm số
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('grades.create') }}" class="btn btn-info btn-block py-3">
                                <i class="fas fa-plus-circle mr-2"></i> Nhập điểm mới
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 