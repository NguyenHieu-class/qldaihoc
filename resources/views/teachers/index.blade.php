@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách giáo viên') }}</span>
                    @if(auth()->user()->role == 'admin')
                    <a href="{{ route('teachers.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm giáo viên mới') }}
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <div class="mb-3">
                        <form action="{{ route('teachers.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <select name="faculty_id" class="form-select">
                                    <option value="">{{ __('-- Tất cả khoa --') }}</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Tìm kiếm theo tên, mã giáo viên hoặc email...') }}" value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i> {{ __('Tìm kiếm') }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary w-100">
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
                                    <th width="10%">{{ __('Mã GV') }}</th>
                                    <th width="20%">{{ __('Họ tên') }}</th>
                                    <th width="15%">{{ __('Email') }}</th>
                                    <th width="10%">{{ __('Số điện thoại') }}</th>
                                    <th width="15%">{{ __('Khoa') }}</th>
                                    <th width="10%">{{ __('Giới tính') }}</th>
                                    @if(auth()->user()->role == 'admin')
                                    <th width="15%">{{ __('Thao tác') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teachers as $key => $teacher)
                                <tr>
                                    <td>{{ $teachers->firstItem() + $key }}</td>
                                    <td>{{ $teacher->teacher_id }}</td>
                                    <td>{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                    <td>{{ $teacher->email }}</td>
                                    <td>{{ $teacher->phone ?? 'N/A' }}</td>
                                    <td>{{ $teacher->faculty->name }}</td>
                                    <td>{{ $teacher->gender }}</td>
                                    @if(auth()->user()->role == 'admin')
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('teachers.show', $teacher->id) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa giáo viên này?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role == 'admin' ? '8' : '7' }}" class="text-center">{{ __('Không có dữ liệu') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $teachers->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 