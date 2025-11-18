@extends('layouts.app')

@section('title', __('Thêm sinh viên vào lớp học phần') . ' - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="mb-1">{{ __('Thêm sinh viên vào lớp :code', ['code' => $classSection->code]) }}</h5>
                        <div class="text-muted small">
                            <span class="me-3">{{ __('Học phần:') }} {{ optional($classSection->subject)->name ?? __('Chưa cập nhật') }}</span>
                            <span>{{ __('Học kỳ:') }} {{ optional(optional($classSection->courseOffering)->semester)->name ?? __('Chưa cập nhật') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('class-sections.show', $classSection) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('Quay lại chi tiết lớp') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end mb-4">
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold">{{ __('Tìm kiếm sinh viên') }}</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Nhập tên, mã sinh viên hoặc email') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="faculty_id" class="form-label fw-semibold">{{ __('Khoa') }}</label>
                            <select class="form-select" id="faculty_id" name="faculty_id">
                                <option value="">{{ __('Tất cả khoa') }}</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="major_id" class="form-label fw-semibold">{{ __('Ngành học') }}</label>
                            <select class="form-select" id="major_id" name="major_id">
                                <option value="">{{ __('Tất cả ngành') }}</option>
                                @foreach($majors as $major)
                                    <option value="{{ $major->id }}" {{ request('major_id') == $major->id ? 'selected' : '' }}>
                                        {{ $major->name }} ({{ optional($major->faculty)->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> {{ __('Lọc') }}
                            </button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('class-sections.students.store', $classSection) }}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                        <th width="15%">{{ __('Mã sinh viên') }}</th>
                                        <th>{{ __('Họ và tên') }}</th>
                                        <th width="20%">{{ __('Lớp') }}</th>
                                        <th width="20%">{{ __('Ngành / Khoa') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if($students->count())
                                    @foreach($students as $student)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input student-checkbox" name="student_ids[]" value="{{ $student->id }}">
                                            </td>
                                            <td>{{ $student->student_id }}</td>
                                            <td>{{ $student->full_name }}</td>
                                            <td>{{ optional($student->class)->name ?? __('Chưa cập nhật') }}</td>
                                            <td>
                                                {{ optional(optional($student->class)->major)->name ?? __('Chưa cập nhật') }}<br>
                                                <small class="text-muted">{{ optional(optional(optional($student->class)->major)->faculty)->name ?? __('Chưa cập nhật') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">{{ __('Không tìm thấy sinh viên phù hợp.') }}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>

                        @error('student_ids')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3">
                            <div class="text-muted small">
                                {{ __('Hiển thị :count trên tổng :total sinh viên', ['count' => $students->count(), 'total' => $students->total()]) }}
                            </div>
                            <button type="submit" class="btn btn-success" @if(!$students->count()) disabled @endif>
                                <i class="fas fa-user-plus me-1"></i> {{ __('Thêm sinh viên đã chọn') }}
                            </button>
                        </div>
                    </form>

                    <div class="mt-3">
                        {{ $students->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.student-checkbox');

        if (!selectAll) {
            return;
        }

        selectAll.addEventListener('change', () => {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        });
    });
</script>
@endpush
@endsection
