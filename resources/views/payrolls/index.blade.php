@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Bảng lương</span>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('payrolls.export', request()->query()) }}" class="btn btn-sm btn-primary">Xuất PDF</a>
                    @else
                        <a href="{{ route('payrolls.export_detail', array_merge(['teacher' => $teacher->id], request()->query())) }}" class="btn btn-sm btn-primary">Xuất PDF</a>
                    @endif
                </div>
                <div class="card-body">
                    @include('partials.alerts')

                    <div class="mb-3">
                        <form action="{{ route('payrolls.index') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <select name="academic_year_id" class="form-select">
                                    <option value="">{{ __('-- Năm học --') }}</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="semester_id" class="form-select">
                                    <option value="">{{ __('-- Học kỳ --') }}</option>
                                    @foreach($semesters as $s)
                                        <option value="{{ $s->id }}" {{ request('semester_id') == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
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

                    @if(isset($sections))
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        @if(Auth::user()->role === 'admin')
                                            <th>Giáo viên</th>
                                        @endif
                                        <th>Mã lớp HP</th>
                                        <th>Môn học</th>
                                        <th>Số tiết</th>
                                        <th>Sĩ số</th>
                                        <th>Lương</th>
                                        <th width="10%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sections as $i => $section)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            @if(Auth::user()->role === 'admin')
                                                <td>{{ $section->teacher->full_name }}</td>
                                            @endif
                                            <td>{{ $section->code }}</td>
                                            <td>{{ $section->subject->name }}</td>
                                            <td>{{ $section->period_count }}</td>
                                            <td>{{ $section->student_count }}</td>
                                            <td>{{ number_format($section->salary, 2) }}</td>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
