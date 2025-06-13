@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách sinh viên') }}</span>
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'teacher')
                    <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm sinh viên mới') }}
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <div class="mb-3">
                        <form action="{{ route('students.index') }}" method="GET" class="row g-3">
                            <div class="col-md-2">
                                <select name="faculty_id" class="form-select">
                                    <option value="">{{ __('-- Tất cả khoa --') }}</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="major_id" class="form-select">
                                    <option value="">{{ __('-- Tất cả ngành --') }}</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->id }}" {{ request('major_id') == $major->id ? 'selected' : '' }}>
                                            {{ $major->name }}
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
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Tìm kiếm theo tên, mã sinh viên hoặc email...') }}" value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo"></i> {{ __('Làm mới') }}
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">{{ __('STT') }}</th>
                                    <th width="10%">{{ __('Mã SV') }}</th>
                                    <th width="15%">{{ __('Họ tên') }}</th>
                                    <th width="15%">{{ __('Email') }}</th>
                                    <th width="10%">{{ __('Lớp') }}</th>
                                    <th width="10%">{{ __('Ngành') }}</th>
                                    <th width="10%">{{ __('Khoa') }}</th>
                                    <th width="10%">{{ __('Giới tính') }}</th>
                                    <th width="15%">{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $key => $student)
                                <tr>
                                    <td>{{ $students->firstItem() + $key }}</td>
                                    <td>{{ $student->student_id }}</td>
                                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->class->name }}</td>
                                    <td>{{ $student->class->major->name }}</td>
                                    <td>{{ $student->class->major->faculty->name }}</td>
                                    <td>{{ $student->gender }}</td>
                                    <td>
                                        <div class="d-flex">
                                            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'teacher')
                                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('students.transcript', $student->id) }}" class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            @if(auth()->user()->role == 'admin')
                                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa sinh viên này?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
                        {{ $students->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 