@extends('layouts.app')

@section('title', 'Bảng điểm sinh viên - Hệ thống quản lý sinh viên')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Bảng điểm sinh viên</h2>
        <div>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i> In bảng điểm
            </button>
        </div>
    </div>

    <!-- Thông tin sinh viên -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin sinh viên</h6>
                </div>
                <div class="card-body">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê điểm số -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê điểm số</h6>
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
                                            @php
                                                $totalScore = 0;
                                                $totalCredits = 0;
                                                
                                                foreach ($student->grades as $grade) {
                                                    if ($grade->total_score !== null) {
                                                        $credits = $grade->subject->credits;
                                                        $totalScore += $grade->total_score * $credits;
                                                        $totalCredits += $credits;
                                                    }
                                                }
                                                
                                                $averageScore = $totalCredits > 0 ? $totalScore / $totalCredits : 0;
                                            @endphp
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
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $student->grades->count() }}</div>
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

    <!-- Bảng điểm chi tiết -->
    @if($groupedGrades->count() > 0)
        @foreach($groupedGrades as $academicYear => $semesters)
            @foreach($semesters as $semester => $grades)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Năm học {{ $academicYear }} - {{ $semester }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>STT</th>
                                                <th>Mã môn học</th>
                                                <th>Tên môn học</th>
                                                <th>Số tín chỉ</th>
                                                <th>Điểm giữa kỳ</th>
                                                <th>Điểm cuối kỳ</th>
                                                <th>Điểm bài tập</th>
                                                <th>Điểm tổng kết</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $semesterTotalScore = 0;
                                                $semesterTotalCredits = 0;
                                            @endphp
                                            
                                            @foreach($grades as $index => $grade)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $grade->subject->code }}</td>
                                                    <td>{{ $grade->subject->name }}</td>
                                                    <td>{{ $grade->subject->credits }}</td>
                                                    <td>{{ $grade->midterm_score ?? 'N/A' }}</td>
                                                    <td>{{ $grade->final_score ?? 'N/A' }}</td>
                                                    <td>{{ $grade->assignment_score ?? 'N/A' }}</td>
                                                    <td>{{ $grade->total_score ?? 'N/A' }}</td>
                                                </tr>
                                                
                                                @php
                                                    if ($grade->total_score !== null) {
                                                        $semesterTotalScore += $grade->total_score * $grade->subject->credits;
                                                        $semesterTotalCredits += $grade->subject->credits;
                                                    }
                                                @endphp
                                            @endforeach
                                            
                                            @php
                                                $semesterAverageScore = $semesterTotalCredits > 0 ? $semesterTotalScore / $semesterTotalCredits : 0;
                                            @endphp
                                            
                                            <tr class="table-primary">
                                                <td colspan="3" class="text-end"><strong>Điểm trung bình học kỳ:</strong></td>
                                                <td><strong>{{ $semesterTotalCredits }}</strong></td>
                                                <td colspan="3"></td>
                                                <td><strong>{{ number_format($semesterAverageScore, 2) }}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    @else
        <div class="alert alert-info">
            Chưa có dữ liệu điểm số cho sinh viên này.
        </div>
    @endif
</div>

<style>
    @media print {
        .navbar, .sidebar, footer, .btn {
            display: none !important;
        }
        
        .content {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .card {
            break-inside: avoid;
        }
    }
</style>
@endsection 