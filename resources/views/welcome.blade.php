@extends('layouts.app')

@section('title', 'Trang chủ - Hệ thống quản lý sinh viên')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="text-center my-5">
                <h1 class="display-4 fw-bold text-primary">Hệ thống quản lý sinh viên</h1>
                <p class="lead">Giải pháp toàn diện cho việc quản lý thông tin sinh viên, điểm số và hoạt động học tập</p>
            </div>
            
            <div class="row g-4 py-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-user-graduate fa-2x"></i>
                            </div>
                            <h4 class="card-title">Quản lý sinh viên</h4>
                            <p class="card-text">Theo dõi thông tin chi tiết của sinh viên, bao gồm thông tin cá nhân, lớp học và ngành học.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            <h4 class="card-title">Quản lý điểm số</h4>
                            <p class="card-text">Nhập và theo dõi điểm số của sinh viên theo từng môn học, học kỳ và năm học.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                            <h4 class="card-title">Quản lý lớp học</h4>
                            <p class="card-text">Tổ chức và quản lý lớp học, phân công sinh viên vào các lớp học phù hợp.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center my-5">
                @guest
                    <p class="mb-4">Đăng nhập để truy cập vào hệ thống quản lý sinh viên</p>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-2">Đăng nhập</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">Đăng ký</a>
                    @endif
                @else
                    <a href="{{ route('dashboard.index') }}" class="btn btn-primary btn-lg">Đi đến Dashboard</a>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection
