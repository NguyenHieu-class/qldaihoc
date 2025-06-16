@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Bảng lương {{ $teacher->full_name }}</span>
                    <a href="{{ route('payrolls.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.alerts')
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
                                        <td>{{ number_format($row['salary'], 2) }}</td>
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
