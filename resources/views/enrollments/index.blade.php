@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">Các lớp học phần đang mở</div>
                <div class="card-body">
                    @include('partials.alerts')
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã lớp</th>
                                    <th>Môn học</th>
                                    <th>Giáo viên</th>
                                    <th>Phòng</th>
                                    <th>Học kỳ</th>
                                    <th>Số SV</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($availableSections as $section)
                                    <tr>
                                        <td>{{ $section->code }}</td>
                                        <td>{{ $section->subject->code }} - {{ $section->subject->name }}</td>
                                        <td>{{ $section->teacher->full_name }}</td>
                                        <td>{{ $section->room }}</td>
                                        <td>{{ optional($section->courseOffering->semester)->name }} ({{ optional(optional($section->courseOffering->semester)->academicYear)->name }})</td>
                                        <td>{{ $section->students()->count() }} / {{ $section->student_count }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('enrollments.store', $section->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">Đăng ký</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Không có lớp khả dụng</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Lớp đã đăng ký</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã lớp</th>
                                    <th>Môn học</th>
                                    <th>Giáo viên</th>
                                    <th>Học kỳ</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($registrations as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->classSection->code }}</td>
                                        <td>{{ $enrollment->classSection->subject->code }} - {{ $enrollment->classSection->subject->name }}</td>
                                        <td>{{ $enrollment->classSection->teacher->full_name }}</td>
                                        <td>{{ optional($enrollment->classSection->courseOffering->semester)->name }} ({{ optional(optional($enrollment->classSection->courseOffering->semester)->academicYear)->name }})</td>
                                        <td>
                                            <form method="POST" action="{{ route('enrollments.destroy', $enrollment->id) }}" onsubmit="return confirm('Hủy đăng ký?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hủy</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Chưa đăng ký lớp nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
