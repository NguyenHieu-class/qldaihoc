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

                    @php($gradeInputsDisabled = !$semester || !$academicYear || $isClassClosed)

                    @if ($isClassClosed)
                        <div class="alert alert-info">
                            <i class="fas fa-lock me-2"></i>
                            {{ __('Lớp học phần đã đóng, bạn chỉ có thể xem lại điểm và không thể chỉnh sửa thêm.') }}
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
                                        <th scope="col" width="10%" class="text-center">{{ __('Thao tác') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $index => $student)
                                        @php
                                            $grade = $grades[$student->id] ?? null;
                                            $isCurrentForm = old('form_student_id') == $student->id;
                                            $midtermValue = $isCurrentForm ? old('midterm_score') : ($grade->midterm_score ?? null);
                                            $finalValue = $isCurrentForm ? old('final_score') : ($grade->final_score ?? null);
                                            $assignmentValue = $isCurrentForm ? old('assignment_score') : ($grade->assignment_score ?? null);
                                            $formId = 'grade-form-' . $student->id;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $student->student_id }}</td>
                                            <td>{{ $student->full_name }}</td>
                                            <td>{{ optional($student->class)->name ?? __('Chưa cập nhật') }}</td>
                                            <td>
                                                <input type="number" step="0.1" min="0" max="10" name="midterm_score" form="{{ $formId }}" class="form-control form-control-sm @if($isCurrentForm && $errors->has('midterm_score')) is-invalid @endif" placeholder="{{ __('Nhập điểm') }}" value="{{ $midtermValue }}" {{ $gradeInputsDisabled ? 'disabled' : '' }}>
                                                @if ($isCurrentForm && $errors->has('midterm_score'))
                                                    <div class="invalid-feedback">{{ $errors->first('midterm_score') }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <input type="number" step="0.1" min="0" max="10" name="final_score" form="{{ $formId }}" class="form-control form-control-sm @if($isCurrentForm && $errors->has('final_score')) is-invalid @endif" placeholder="{{ __('Nhập điểm') }}" value="{{ $finalValue }}" {{ $gradeInputsDisabled ? 'disabled' : '' }}>
                                                @if ($isCurrentForm && $errors->has('final_score'))
                                                    <div class="invalid-feedback">{{ $errors->first('final_score') }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <input type="number" step="0.1" min="0" max="10" name="assignment_score" form="{{ $formId }}" class="form-control form-control-sm @if($isCurrentForm && $errors->has('assignment_score')) is-invalid @endif" placeholder="{{ __('Nhập điểm') }}" value="{{ $assignmentValue }}" {{ $gradeInputsDisabled ? 'disabled' : '' }}>
                                                @if ($isCurrentForm && $errors->has('assignment_score'))
                                                    <div class="invalid-feedback">{{ $errors->first('assignment_score') }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $grade?->total_score !== null ? number_format($grade->total_score, 1) : __('--') }}</span>
                                            </td>
                                            <td class="text-center">
                                                <form id="{{ $formId }}" action="{{ route('teacher.classes.gradebook.store', [$classSection, $student]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="form_student_id" value="{{ $student->id }}">
                                                    <button type="submit" class="btn btn-primary btn-sm" {{ $gradeInputsDisabled ? 'disabled' : '' }}>
                                                        <i class="fas fa-save me-1"></i>{{ __('Lưu') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
