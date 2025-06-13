@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách điểm số') }}</span>
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'teacher')
                    <a href="{{ route('grades.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm điểm mới') }}
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <div class="mb-3">
                        <form action="{{ route('grades.index') }}" method="GET" class="row g-3">
                            <div class="col-md-2">
                                <select name="student_id" class="form-select">
                                    <option value="">{{ __('-- Tất cả sinh viên --') }}</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->student_id }} - {{ $student->first_name }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="subject_id" class="form-select">
                                    <option value="">{{ __('-- Tất cả môn học --') }}</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->code }} - {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="class_id" class="form-select">
                                    <option value="">{{ __('-- Tất cả lớp --') }}</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="semester" class="form-select">
                                    <option value="">{{ __('-- Tất cả học kỳ --') }}</option>
                                    @foreach($semesters as $semester)
                                        <option value="{{ $semester }}" {{ request('semester') == $semester ? 'selected' : '' }}>
                                            {{ $semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="academic_year" class="form-select">
                                    <option value="">{{ __('-- Tất cả năm học --') }}</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-outline-primary me-1">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('grades.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">{{ __('STT') }}</th>
                                    <th width="15%">{{ __('Sinh viên') }}</th>
                                    <th width="15%">{{ __('Môn học') }}</th>
                                    <th width="10%">{{ __('Điểm giữa kỳ') }}</th>
                                    <th width="10%">{{ __('Điểm cuối kỳ') }}</th>
                                    <th width="10%">{{ __('Điểm bài tập') }}</th>
                                    <th width="10%">{{ __('Điểm tổng kết') }}</th>
                                    <th width="10%">{{ __('Học kỳ') }}</th>
                                    <th width="15%">{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grades as $key => $grade)
                                <tr>
                                    <td>{{ $grades->firstItem() + $key }}</td>
                                    <td>
                                        <a href="{{ route('students.show', $grade->student->id) }}">
                                            {{ $grade->student->student_id }} - {{ $grade->student->first_name }} {{ $grade->student->last_name }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('subjects.show', $grade->subject->id) }}">
                                            {{ $grade->subject->code }} - {{ $grade->subject->name }}
                                        </a>
                                    </td>
                                    <td>{{ $grade->midterm_score ?? 'N/A' }}</td>
                                    <td>{{ $grade->final_score ?? 'N/A' }}</td>
                                    <td>{{ $grade->assignment_score ?? 'N/A' }}</td>
                                    <td>{{ $grade->total_score ?? 'N/A' }}</td>
                                    <td>{{ $grade->semester }} ({{ $grade->academic_year }})</td>
                                    <td>
                                        <div class="d-flex">
                                            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'teacher')
                                            <a href="{{ route('grades.edit', $grade->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('grades.show', $grade->id) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('grades.destroy', $grade->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa điểm này?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @else
                                            <a href="{{ route('grades.show', $grade->id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">{{ __('Không có dữ liệu') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $grades->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 