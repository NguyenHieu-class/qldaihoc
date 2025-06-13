@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết sinh viên') }}</span>
                    <div>
                        <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm me-1">
                            <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                        </a>
                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'teacher')
                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-primary btn-sm">
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
                            <div class="col-md-8">{{ $student->student_id }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Họ và tên:') }}</div>
                            <div class="col-md-8">{{ $student->first_name }} {{ $student->last_name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Giới tính:') }}</div>
                            <div class="col-md-8">{{ $student->gender }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Ngày sinh:') }}</div>
                            <div class="col-md-8">{{ $student->date_of_birth->format('d/m/Y') }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Email:') }}</div>
                            <div class="col-md-8">{{ $student->email }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Số điện thoại:') }}</div>
                            <div class="col-md-8">{{ $student->phone ?? __('Chưa cập nhật') }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Địa chỉ:') }}</div>
                            <div class="col-md-8">{{ $student->address ?? __('Chưa cập nhật') }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin học tập') }}</h5>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Lớp:') }}</div>
                            <div class="col-md-8">{{ $student->class->name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Ngành học:') }}</div>
                            <div class="col-md-8">{{ $student->class->major->name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Khoa:') }}</div>
                            <div class="col-md-8">{{ $student->class->major->faculty->name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Năm học:') }}</div>
                            <div class="col-md-8">{{ $student->class->year }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h5 class="mb-3">{{ __('Thống kê học tập') }}</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">{{ __('Điểm trung bình') }}</h6>
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
                                        <p class="card-text fs-2">{{ number_format($averageScore, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">{{ __('Số môn đã học') }}</h6>
                                        <p class="card-text fs-2">{{ $student->grades->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">{{ __('Tín chỉ tích lũy') }}</h6>
                                        <p class="card-text fs-2">{{ $totalCredits }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>{{ __('Bảng điểm') }}</h5>
                        <a href="{{ route('students.transcript', $student->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-chart-bar"></i> {{ __('Xem bảng điểm đầy đủ') }}
                        </a>
                    </div>

                    @if($student->grades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Mã môn') }}</th>
                                        <th>{{ __('Tên môn học') }}</th>
                                        <th>{{ __('Tín chỉ') }}</th>
                                        <th>{{ __('Điểm tổng kết') }}</th>
                                        <th>{{ __('Học kỳ') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->grades->take(5) as $grade)
                                        <tr>
                                            <td>{{ $grade->subject->code }}</td>
                                            <td>{{ $grade->subject->name }}</td>
                                            <td>{{ $grade->subject->credits }}</td>
                                            <td>{{ $grade->total_score ?? 'N/A' }}</td>
                                            <td>{{ $grade->semester }} ({{ $grade->academic_year }})</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ __('Sinh viên chưa có điểm.') }}
                        </div>
                    @endif

                    @if(auth()->user()->role == 'admin')
                    <div class="d-flex justify-content-end mt-4">
                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa sinh viên này?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> {{ __('Xóa sinh viên') }}
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