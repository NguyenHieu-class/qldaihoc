@extends('layouts.app')

@section('title', 'Student Dashboard - Hệ thống quản lý sinh viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Dashboard Sinh viên</h2>
    </div>

    <!-- Thông tin sinh viên -->
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
                                    <p class="mb-1"><strong>Mã sinh viên:</strong></p>
                                    <p>{{ $student->student_id }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Họ tên:</strong></p>
                                    <p>{{ $student->first_name }} {{ $student->last_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Email:</strong></p>
                                    <p>{{ $student->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Số điện thoại:</strong></p>
                                    <p>{{ $student->phone ?? 'Chưa cập nhật' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Lớp:</strong></p>
                                    <p>{{ $student->class->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Ngành học:</strong></p>
                                    <p>{{ $student->class->major->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Khoa:</strong></p>
                                    <p>{{ $student->class->major->faculty->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><strong>Ngày sinh:</strong></p>
                                    <p>{{ $student->date_of_birth->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê điểm số -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê điểm số</h6>
                    <a href="{{ route('students.transcript', $student->id) }}" class="btn btn-sm btn-primary">Xem bảng điểm đầy đủ</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Điểm trung bình tích lũy</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averageScore, 2) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                                Số môn học đã học</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $grades->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
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
                                                Số tín chỉ đã tích lũy</div>
                                            @php
                                                $totalCredits = 0;
                                                foreach ($grades as $grade) {
                                                    if ($grade->total_score !== null) {
                                                        $totalCredits += $grade->subject->credits;
                                                    }
                                                }
                                            @endphp
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCredits }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
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

    <!-- Điểm số gần đây -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Điểm số gần đây</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Mã môn học</th>
                                    <th>Tên môn học</th>
                                    <th>Số tín chỉ</th>
                                    <th>Điểm giữa kỳ</th>
                                    <th>Điểm cuối kỳ</th>
                                    <th>Điểm bài tập</th>
                                    <th>Điểm tổng kết</th>
                                    <th>Học kỳ</th>
                                    <th>Năm học</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grades->take(5) as $grade)
                                    <tr>
                                        <td>{{ $grade->subject->code }}</td>
                                        <td>{{ $grade->subject->name }}</td>
                                        <td>{{ $grade->subject->credits }}</td>
                                        <td>{{ $grade->midterm_score ?? 'N/A' }}</td>
                                        <td>{{ $grade->final_score ?? 'N/A' }}</td>
                                        <td>{{ $grade->assignment_score ?? 'N/A' }}</td>
                                        <td>{{ $grade->total_score ?? 'N/A' }}</td>
                                        <td>{{ $grade->semester }}</td>
                                        <td>{{ $grade->academic_year }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 