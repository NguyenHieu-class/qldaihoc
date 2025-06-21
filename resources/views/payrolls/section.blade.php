@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Bảng lương lớp {{ $section->code }}</span>
                    <div>
                        <a href="{{ route('payrolls.section_export', $section) }}" class="btn btn-primary btn-sm me-2">Xuất PDF</a>
                        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Giáo viên</th>
                                <td>{{ $teacher->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Môn học</th>
                                <td>{{ $section->subject->name }}</td>
                            </tr>
                            <tr>
                                <th>Số tiết</th>
                                <td>{{ $section->period_count }}</td>
                            </tr>
                            <tr>
                                <th>Sĩ số</th>
                                <td>{{ $section->student_count }}</td>
                            </tr>
                            <tr>
                                <th>Hs học vị</th>
                                <td>{{ $detail['degree'] }}</td>
                            </tr>
                            <tr>
                                <th>Hs sĩ số</th>
                                <td>{{ $detail['class'] }}</td>
                            </tr>
                            <tr>
                                <th>Hs môn</th>
                                <td>{{ $detail['subject'] }}</td>
                            </tr>
                            <tr>
                                <th>Lương</th>
                                <td>{{ number_format($detail['salary'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
