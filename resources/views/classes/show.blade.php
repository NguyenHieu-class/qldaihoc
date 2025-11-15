@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết lớp học') }}</span>
                    <div>
                        <a href="{{ route('classes.index') }}" class="btn btn-secondary btn-sm me-1">
                            <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                        </a>
                        @if(auth()->user()->role == 'admin')
                        <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Chỉnh sửa') }}
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @php($user = auth()->user())

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin lớp học') }}</h5>
                        <div class="row">
                            <div class="col-md-4 fw-bold">{{ __('Tên lớp học:') }}</div>
                            <div class="col-md-8">{{ $class->name }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Mã lớp học:') }}</div>
                            <div class="col-md-8">{{ $class->code }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Năm học:') }}</div>
                            <div class="col-md-8">{{ $class->year }}</div>
                        </div>
                        @if($user->isAdmin() || $user->isTeacher())
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Trạng thái:') }}</div>
                            <div class="col-md-8">
                                <span class="badge bg-{{ $class->status_badge_class }}">{{ $class->status_label }}</span>
                            </div>
                        </div>
                        @endif
                        @if($user->isAdmin())
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Ngành học:') }}</div>
                            <div class="col-md-8">
                                <a href="{{ route('majors.show', $class->major->id) }}">
                                    {{ $class->major->name }}
                                </a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 fw-bold">{{ __('Khoa:') }}</div>
                            <div class="col-md-8">
                                <a href="{{ route('faculties.show', $class->major->faculty->id) }}">
                                    {{ $class->major->faculty->name }}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Danh sách sinh viên') }}</h5>
                        @if($class->students->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">{{ __('STT') }}</th>
                                            <th width="15%">{{ __('Mã SV') }}</th>
                                            <th width="30%">{{ __('Họ tên') }}</th>
                                            <th width="15%">{{ __('Giới tính') }}</th>
                                            <th width="20%">{{ __('Email') }}</th>
                                            @if($user->isAdmin() || $user->isTeacher())
                                            <th width="15%">{{ __('Thao tác') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($class->students as $key => $student)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $student->student_id }}</td>
                                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                            <td>{{ $student->gender }}</td>
                                            <td>{{ $student->email }}</td>
                                            @if($user->isAdmin() || $user->isTeacher())
                                            <td>
                                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('students.transcript', $student->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center">{{ __('Không có sinh viên nào trong lớp này') }}</p>
                        @endif
                    </div>

                    @if($user->isAdmin())
                    <div class="d-flex justify-content-end mt-4">
                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa lớp học này?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> {{ __('Xóa lớp học') }}
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 