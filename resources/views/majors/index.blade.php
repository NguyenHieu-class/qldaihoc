@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách ngành học') }}</span>
                    @if(auth()->user()->role == 'admin')
                    <a href="{{ route('majors.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm ngành học mới') }}
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <div class="mb-3">
                        <form action="{{ route('majors.index') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <select name="faculty_id" class="form-select">
                                    <option value="">{{ __('-- Tất cả khoa --') }}</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Tìm kiếm theo tên hoặc mã ngành...') }}" value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i> {{ __('Tìm kiếm') }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('majors.index') }}" class="btn btn-outline-secondary w-100">
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
                                    <th width="15%">{{ __('Mã ngành') }}</th>
                                    <th width="25%">{{ __('Tên ngành') }}</th>
                                    <th width="20%">{{ __('Khoa') }}</th>
                                    <th width="20%">{{ __('Mô tả') }}</th>
                                    @if(auth()->user()->role == 'admin')
                                    <th width="15%">{{ __('Thao tác') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($majors as $key => $major)
                                <tr>
                                    <td>{{ $majors->firstItem() + $key }}</td>
                                    <td>{{ $major->code }}</td>
                                    <td>{{ $major->name }}</td>
                                    <td>{{ $major->faculty->name }}</td>
                                    <td>{{ Str::limit($major->description, 50) }}</td>
                                    @if(auth()->user()->role == 'admin')
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('majors.edit', $major->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('majors.show', $major->id) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('majors.destroy', $major->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa ngành học này?') }}')">
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
                                    <td colspan="{{ auth()->user()->role == 'admin' ? '6' : '5' }}" class="text-center">{{ __('Không có dữ liệu') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $majors->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 