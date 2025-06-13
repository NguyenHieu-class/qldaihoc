@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách môn học') }}</span>
                    @if(auth()->user()->role == 'admin')
                    <a href="{{ route('subjects.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm môn học mới') }}
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <div class="mb-3">
                        <form action="{{ route('subjects.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <select name="credits" class="form-select">
                                    <option value="">{{ __('-- Tất cả số tín chỉ --') }}</option>
                                    @foreach($creditOptions as $credit)
                                        <option value="{{ $credit }}" {{ request('credits') == $credit ? 'selected' : '' }}>
                                            {{ $credit }} {{ __('tín chỉ') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Tìm kiếm theo tên hoặc mã môn học...') }}" value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i> {{ __('Tìm kiếm') }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary w-100">
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
                                    <th width="15%">{{ __('Mã môn học') }}</th>
                                    <th width="35%">{{ __('Tên môn học') }}</th>
                                    <th width="10%">{{ __('Số tín chỉ') }}</th>
                                    <th width="20%">{{ __('Mô tả') }}</th>
                                    @if(auth()->user()->role == 'admin')
                                    <th width="15%">{{ __('Thao tác') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjects as $key => $subject)
                                <tr>
                                    <td>{{ $subjects->firstItem() + $key }}</td>
                                    <td>{{ $subject->code }}</td>
                                    <td>{{ $subject->name }}</td>
                                    <td>{{ $subject->credits }}</td>
                                    <td>{{ Str::limit($subject->description, 50) }}</td>
                                    @if(auth()->user()->role == 'admin')
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('subjects.show', $subject->id) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa môn học này?') }}')">
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
                        {{ $subjects->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 