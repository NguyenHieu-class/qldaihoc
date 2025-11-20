@extends('layouts.app')

@section('title', __('Nhập điểm lớp :code', ['code' => $classSection->code]))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-1">{{ __('Nhập điểm cho lớp: :code', ['code' => $classSection->code]) }}</h5>
                        <div class="small text-muted">
                            <span class="me-3">
                                <strong>{{ __('Môn học:') }}</strong>
                                {{ $classSection->subject->code }} - {{ $classSection->subject->name }}
                            </span>
                            <span class="me-3"><strong>{{ __('Giáo viên:') }}</strong> {{ $teacher->full_name }}</span>
                            <span class="me-3"><strong>{{ __('Học kỳ:') }}</strong> {{ $semester->name ?? __('Chưa cập nhật') }}</span>
                            <span><strong>{{ __('Năm học:') }}</strong> {{ $academicYear->name ?? __('Chưa cập nhật') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('teacher.classes.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Quay lại danh sách lớp') }}
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')

                    @php
                        $gradeInputsDisabled = !$semester || !$academicYear || $isClassClosed || $isGradesLocked;
                    @endphp

                    @if ($isClassClosed)
                        <div class="alert alert-info">
                            <i class="fas fa-lock me-2"></i>
                            {{ __('Lớp học phần đã đóng, bạn chỉ có thể xem lại điểm và không thể chỉnh sửa thêm.') }}
                        </div>
                    @endif

                    @if ($isGradesLocked && !$isClassClosed)
                        <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert">
                            <div>
                                <i class="fas fa-shield-alt me-2"></i>
                                {{ __('Điểm đã được khóa, bạn không thể chỉnh sửa cho đến khi được mở khóa.') }}
                                @if ($classSection->grades_locked_at)
                                    <span class="ms-2 text-muted">{{ __('Khoá lúc:') }} {{ $classSection->grades_locked_at->format('d/m/Y H:i') }}</span>
                                @endif
                                @if ($latestUnlockRequest && $latestUnlockRequest->status !== \App\Models\GradeUnlockRequest::STATUS_PENDING)
                                    <span class="ms-2 text-muted">
                                        {{ __('Kết quả yêu cầu trước:') }} {{ ucfirst($latestUnlockRequest->status) }}
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                @if (!$pendingUnlockRequest)
                                    <form method="POST" action="{{ route('teacher.classes.gradebook.unlock_request', $classSection) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-unlock"></i> {{ __('Gửi yêu cầu mở khóa') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-warning text-dark">{{ __('Đang chờ duyệt') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (!$semester || !$academicYear)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ __('Lớp học phần chưa được gắn học kỳ hoặc năm học, vui lòng liên hệ quản trị viên để cập nhật trước khi nhập điểm.') }}
                        </div>
                    @endif

                    @if ($students->isEmpty())
                        <p class="text-center text-muted my-5">
                            <i class="fas fa-user-graduate me-2"></i>{{ __('Chưa có sinh viên nào đăng ký lớp học phần này.') }}
                        </p>
                    @else
                        <form id="lock-form" action="{{ route('teacher.classes.gradebook.lock', $classSection) }}" method="POST" class="d-none">
                            @csrf
                        </form>

                        <form id="gradebook-form" action="{{ route('teacher.classes.gradebook.store', $classSection) }}" method="POST">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" {{ $gradeInputsDisabled ? 'disabled' : '' }}>
                                        <i class="fas fa-save me-1"></i>{{ __('Lưu tất cả') }}
                                    </button>
                                    @if (!$isClassClosed)
                                        <button type="submit" form="lock-form" class="btn btn-outline-danger" {{ ($isGradesLocked || !$semester || !$academicYear || $students->isEmpty()) ? 'disabled' : '' }} onclick="return confirm('{{ __('Bạn chắc chắn muốn khóa điểm?') }}')">
                                            <i class="fas fa-lock"></i> {{ __('Khoá điểm') }}
                                        </button>
                                    @endif
                                </div>
                                @if ($pendingUnlockRequest && !$isClassClosed)
                                    <span class="badge bg-warning text-dark">{{ __('Đã gửi yêu cầu mở khóa, vui lòng chờ duyệt') }}</span>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" width="5%">#</th>
                                            <th scope="col" width="12%">{{ __('Mã sinh viên') }}</th>
                                            <th scope="col">{{ __('Họ và tên') }}</th>
                                            <th scope="col" width="15%">{{ __('Lớp') }}</th>
                                            <th scope="col" width="12%">{{ __('Điểm giữa kỳ') }}</th>
                                            <th scope="col" width="12%">{{ __('Điểm cuối kỳ') }}</th>
                                            <th scope="col" width="12%">{{ __('Điểm bài tập') }}</th>
                                            <th scope="col" width="10%">{{ __('Điểm tổng') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $index => $student)
                                            @php
                                                $grade = $grades[$student->id] ?? null;
                                                $midtermKey = 'grades.' . $student->id . '.midterm_score';
                                                $finalKey = 'grades.' . $student->id . '.final_score';
                                                $assignmentKey = 'grades.' . $student->id . '.assignment_score';
                                                $midtermValue = old($midtermKey, $grade->midterm_score ?? null);
                                                $finalValue = old($finalKey, $grade->final_score ?? null);
                                                $assignmentValue = old($assignmentKey, $grade->assignment_score ?? null);
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td class="fw-semibold">{{ $student->student_id }}</td>
                                                <td>{{ $student->full_name }}</td>
                                                <td>{{ optional($student->class)->name ?? __('Chưa cập nhật') }}</td>
                                                <td>
                                                    <input type="number" step="0.1" min="0" max="10" name="grades[{{ $student->id }}][midterm_score]" class="form-control form-control-sm @error($midtermKey) is-invalid @enderror" placeholder="{{ __('Nhập điểm') }}" value="{{ $midtermValue }}" {{ $gradeInputsDisabled ? 'disabled' : '' }}>
                                                    @error($midtermKey)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" step="0.1" min="0" max="10" name="grades[{{ $student->id }}][final_score]" class="form-control form-control-sm @error($finalKey) is-invalid @enderror" placeholder="{{ __('Nhập điểm') }}" value="{{ $finalValue }}" {{ $gradeInputsDisabled ? 'disabled' : '' }}>
                                                    @error($finalKey)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" step="0.1" min="0" max="10" name="grades[{{ $student->id }}][assignment_score]" class="form-control form-control-sm @error($assignmentKey) is-invalid @enderror" placeholder="{{ __('Nhập điểm') }}" value="{{ $assignmentValue }}" {{ $gradeInputsDisabled ? 'disabled' : '' }}>
                                                    @error($assignmentKey)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">{{ $grade?->total_score !== null ? number_format($grade->total_score, 1) : __('--') }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
