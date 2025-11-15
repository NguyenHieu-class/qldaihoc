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
                    @if(session('import_errors') && count(session('import_errors')))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">{{ __('Một số dòng không thể nhập') }}</h5>
                            <ul class="mb-0">
                                @foreach(session('import_errors') as $importError)
                                    <li>{{ $importError }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @include('partials.instructions', ['guideline' => 'Sử dụng bộ lọc và ô tìm kiếm để lọc danh sách. Bạn có thể thêm, chỉnh sửa hoặc xoá bản ghi.'])

                    @if(auth()->user()->role == 'admin')
                    <div class="card border border-2 border-light-subtle shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-3">
                                <div>
                                    <h5 class="card-title mb-1">{{ __('Nhập sinh viên từ CSV') }}</h5>
                                    <p class="mb-0 text-muted small">{{ __('Tải file mẫu, điền thông tin sinh viên và tải lên để tạo tài khoản với mật khẩu mặc định là ngày sinh (ddmmyyyy).') }}</p>
                                </div>
                                <a href="{{ route('students.template') }}" class="btn btn-outline-success">
                                    <i class="fas fa-download"></i> {{ __('Tải file mẫu') }}
                                </a>
                            </div>
                            <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-center">
                                @csrf
                                <div class="col-md-8">
                                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                                </div>
                                <div class="col-md-4 d-grid d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> {{ __('Tải lên và nhập') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

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