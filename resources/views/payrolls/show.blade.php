@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Bảng lương {{ $teacher->full_name }}</span>
                    <div>
                        <a href="{{ route('payrolls.export_detail', $teacher) }}" class="btn btn-primary btn-sm me-2">Xuất PDF</a>
                        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('partials.alerts')

                    <div class="mb-3">
                        <form action="{{ route('payrolls.show', $teacher) }}" method="GET" class="row g-3">
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
                                <a href="{{ route('payrolls.show', $teacher) }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Mã lớp HP</th>
                                    <th>Môn học</th>
                                    <th>Số tiết</th>
                                    <th>Sĩ số</th>
                                    <th>Hs học vị</th>
                                    <th>Hs sĩ số</th>
                                    <th>Hs môn</th>
                                    <th>Lương</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($details as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $row['section']->code }}</td>
                                        <td>{{ $row['section']->subject->name }}</td>
                                        <td>{{ $row['section']->period_count }}</td>
                                        <td>{{ $row['section']->student_count }}</td>
                                        <td>{{ $row['degree'] }}</td>
                                        <td>{{ $row['class'] }}</td>
                                        <td>{{ $row['subject'] }}</td>
                                        <td>
                                            {{ number_format($row['salary'], 2) }}
                                            <a href="{{ route('payrolls.section', $row['section']) }}" class="ms-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end fw-bold">
                        Tổng lương: {{ number_format($total, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
