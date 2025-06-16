@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Bảng lương</span>
                    @if(isset($teachers))
                        <a href="{{ route('payrolls.export') }}" class="btn btn-sm btn-primary">Xuất PDF</a>
                    @elseif(isset($teacher))
                        <a href="{{ route('payrolls.export_detail', $teacher) }}" class="btn btn-sm btn-primary">Xuất PDF</a>
                    @endif
                </div>
                <div class="card-body">
                    @include('partials.alerts')

                    @if(isset($teachers))
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Mã GV</th>
                                        <th>Họ tên</th>
                                        <th>Tổng lương</th>
                                        <th width="10%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teachers as $index => $t)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $t->teacher_id }}</td>
                                            <td>{{ $t->full_name }}</td>
                                            <td>{{ number_format($t->total_salary, 2) }}</td>
                                            <td>
                                                <a href="{{ route('payrolls.show', $t->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(isset($sections))
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end fw-bold mt-3">
                            Tổng lương: {{ number_format($sections->sum('salary'), 2) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
