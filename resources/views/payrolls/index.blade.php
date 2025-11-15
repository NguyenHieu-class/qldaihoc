@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Bảng lương</span>
                    @if(Auth::user()->role !== 'admin')
                        <a href="{{ route('payrolls.export_detail', array_merge(['teacher' => $teacher->id], request()->query())) }}" class="btn btn-sm btn-primary">Xuất PDF</a>
                    @endif
                </div>
                <div class="card-body">
                    @include('partials.alerts')
                    @include('partials.instructions', ['guideline' => 'Sử dụng bộ lọc và ô tìm kiếm để lọc danh sách. Bạn có thể thêm, chỉnh sửa hoặc xoá bản ghi.'])

                    <div class="mb-3">
                        <form action="{{ route('payrolls.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <select name="academic_year_id" class="form-select">
                                    <option value="">{{ __('-- Năm học --') }}</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="semester_id" class="form-select">
                                    <option value="">{{ __('-- Học kỳ --') }}</option>
                                    @foreach($semesters as $s)
                                        <option value="{{ $s->id }}" {{ request('semester_id') == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(Auth::user()->role === 'admin')
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Tìm kiếm mã GV, mã môn, mã lớp hoặc tên...') }}" value="{{ request('search') }}">
                                </div>
                            @endif
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </form>
                    </div>

                    @if(Auth::user()->role === 'admin' && $shouldPromptFilters)
                        <div class="alert alert-info">
                            Vui lòng chọn năm học và học kỳ hoặc sử dụng ô tìm kiếm để hiển thị bảng lương.
                        </div>
                    @elseif($filterApplied && $sections->isEmpty())
                        <div class="alert alert-warning">
                            Không tìm thấy dữ liệu phù hợp với bộ lọc.
                        </div>
                    @elseif($sections->isNotEmpty())
                        @if(Auth::user()->role === 'admin')
                            <form id="export-form" action="{{ route('payrolls.export_selected') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>#</th>
                                            <th>Giáo viên</th>
                                            <th>Mã lớp HP</th>
                                            <th>Môn học</th>
                                            <th>Số tiết</th>
                                            <th>Sĩ số</th>
                                            <th>Lương</th>
                                            <th>Trạng thái</th>
                                            <th width="10%">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sections as $i => $section)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="section_ids[]" value="{{ $section->id }}" class="section-checkbox" form="export-form">
                                                </td>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $section->teacher->full_name }}</td>
                                                <td>{{ $section->code }}</td>
                                                <td>{{ $section->subject->name }}</td>
                                                <td>{{ $section->period_count }}</td>
                                                <td>{{ $section->student_count }}</td>
                                                <td>{{ number_format($section->salary, 2) }}</td>
                                                <td>
                                                    <form action="{{ route('payrolls.section_payment_status', $section) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="payment_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                            @foreach($paymentStatuses as $value => $label)
                                                                <option value="{{ $value }}" {{ $section->payment_status === $value ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="{{ route('payrolls.section', $section) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <button type="submit" class="btn btn-primary" form="export-form">
                                    <i class="fas fa-print me-1"></i> In lương đã chọn
                                </button>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Mã lớp HP</th>
                                            <th>Môn học</th>
                                            <th>Số tiết</th>
                                            <th>Sĩ số</th>
                                            <th>Lương</th>
                                            <th>Trạng thái</th>
                                            <th width="10%">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sections as $i => $section)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $section->code }}</td>
                                                <td>{{ $section->subject->name }}</td>
                                                <td>{{ $section->period_count }}</td>
                                                <td>{{ $section->student_count }}</td>
                                                <td>{{ number_format($section->salary, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $section->payment_status_label }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('payrolls.section', $section) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end fw-bold mt-3">
                                Tổng lương: {{ number_format($total, 2) }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @if(Auth::user()->role === 'admin')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectAll = document.getElementById('select-all');
                if (!selectAll) {
                    return;
                }

                const checkboxes = document.querySelectorAll('.section-checkbox');

                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAll.checked;
                    });
                });
            });
        </script>
    @endif
@endpush
