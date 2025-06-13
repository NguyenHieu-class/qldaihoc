@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách lớp học') }}</span>
                    @if(auth()->user()->role == 'admin')
                    <a href="{{ route('classes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm lớp học mới') }}
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <div class="mb-3">
                        <form action="{{ route('classes.index') }}" method="GET" class="row g-3">
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
                            <div class="col-md-3">
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
                                <select name="year" class="form-select">
                                    <option value="">{{ __('-- Tất cả năm --') }}</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Tìm kiếm...') }}" value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">{{ __('STT') }}</th>
                                    <th width="15%">{{ __('Mã lớp') }}</th>
                                    <th width="20%">{{ __('Tên lớp') }}</th>
                                    <th width="20%">{{ __('Ngành học') }}</th>
                                    <th width="15%">{{ __('Khoa') }}</th>
                                    <th width="10%">{{ __('Năm học') }}</th>
                                    @if(auth()->user()->role == 'admin')
                                    <th width="15%">{{ __('Thao tác') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classes as $key => $class)
                                <tr>
                                    <td>{{ $classes->firstItem() + $key }}</td>
                                    <td>{{ $class->code }}</td>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->major->name }}</td>
                                    <td>{{ $class->major->faculty->name }}</td>
                                    <td>{{ $class->year }}</td>
                                    @if(auth()->user()->role == 'admin')
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('classes.show', $class->id) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa lớp học này?') }}')">
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
                                    <td colspan="{{ auth()->user()->role == 'admin' ? '7' : '6' }}" class="text-center">{{ __('Không có dữ liệu') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $classes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 