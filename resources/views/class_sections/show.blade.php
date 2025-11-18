@extends('layouts.app')

@section('title', __('Chi tiết lớp học phần') . ' - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ __('Thông tin lớp học phần') }}</h5>
                        <small class="text-muted">{{ __('Mã lớp:') }} {{ $classSection->code }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        @php($user = auth()->user())
                        @if($user && $user->isAdmin())
                            <a href="{{ route('class-sections.edit', $classSection->id) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-edit"></i> {{ __('Chỉnh sửa') }}
                            </a>
                        @endif
                        @if($backRoute)
                            <a href="{{ $backRoute }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3">{{ __('Thông tin học phần') }}</h6>
                            <div class="mb-2">
                                <span class="fw-semibold">{{ __('Tên học phần:') }}</span>
                                <span>{{ optional($classSection->subject)->name ?? __('Chưa cập nhật') }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="fw-semibold">{{ __('Mã học phần:') }}</span>
                                <span>{{ optional($classSection->subject)->code ?? __('Chưa cập nhật') }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="fw-semibold">{{ __('Số tín chỉ:') }}</span>
                                <span>{{ optional($classSection->subject)->credits ?? __('Chưa cập nhật') }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="fw-semibold">{{ __('Số tiết:') }}</span>
                                <span>{{ $classSection->period_count ?? __('Chưa cập nhật') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3">{{ __('Thông tin giảng dạy') }}</h6>
                            <div class="mb-2">
                                <span class="fw-semibold">{{ __('Tên lớp học phần:') }}</span>
                                <span>{{ $classSection->code }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="fw-semibold">{{ __('Giáo viên phụ trách:') }}</span>
                                <span>{{ optional($classSection->teacher)->full_name ?? __('Chưa cập nhật') }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="fw-semibold">{{ __('Học kỳ:') }}</span>
                                <span>{{ $semester->name ?? __('Chưa cập nhật') }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="fw-semibold">{{ __('Năm học:') }}</span>
                                <span>{{ $academicYear->name ?? __('Chưa cập nhật') }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="fw-semibold">{{ __('Trạng thái:') }}</span>
                                <span class="badge bg-secondary">{{ $classSection->status_label }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">{{ __('Mô tả chi tiết') }}</h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <span class="fw-semibold">{{ __('Phòng học:') }}</span>
                                <span>{{ $classSection->room ?? __('Chưa xếp') }}</span>
                            </div>
                            <div class="col-md-4 mb-2">
                                <span class="fw-semibold">{{ __('Mức thù lao:') }}</span>
                                @php($teachingRate = $classSection->teachingRate)
                                <span>
                                    @if($teachingRate)
                                        {{ number_format($teachingRate->amount, 0, ',', '.') }} ₫
                                    @else
                                        {{ __('Chưa cập nhật') }}
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-4 mb-2">
                                <span class="fw-semibold">{{ __('Số sinh viên tối đa:') }}</span>
                                <span>{{ $classSection->student_count ?? __('Chưa cập nhật') }}</span>
                            </div>
                            <div class="col-md-4 mb-2">
                                <span class="fw-semibold">{{ __('Số sinh viên đã đăng ký:') }}</span>
                                <span>{{ $classSection->students->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        @if($user && $user->isAdmin())
                            <div class="mb-4">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3">{{ __('Thêm sinh viên vào lớp học phần') }}</h6>
                                <form method="POST" action="{{ route('class-sections.students.store', $classSection) }}" class="row g-3 align-items-end">
                                    @csrf
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold" for="student_id">{{ __('Chọn sinh viên') }}</label>
                                        <select name="student_id" id="student_id" class="form-select" required>
                                            <option value="">{{ __('-- Chọn sinh viên --') }}</option>
                                            @forelse($availableStudents as $student)
                                                <option value="{{ $student->id }}">{{ $student->student_id }} - {{ $student->full_name }}</option>
                                            @empty
                                                <option value="" disabled>{{ __('Tất cả sinh viên đã đăng ký lớp này.') }}</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button class="btn btn-success w-100" type="submit" @if($availableStudents->isEmpty()) disabled @endif>
                                            <i class="fas fa-user-plus"></i> {{ __('Thêm sinh viên') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        <h5 class="mb-3">{{ __('Danh sách sinh viên đăng ký') }}</h5>
                        @if($classSection->students->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="10%">{{ __('STT') }}</th>
                                            <th width="20%">{{ __('Mã sinh viên') }}</th>
                                            <th>{{ __('Họ và tên') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($classSection->students as $index => $student)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $student->student_id }}</td>
                                                <td>{{ $student->full_name }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted fst-italic">{{ __('Chưa có sinh viên đăng ký lớp học phần này.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
